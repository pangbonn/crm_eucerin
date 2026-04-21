<template>
  <div class="pb-20">
    <div class="bg-primary text-primary-content py-4 px-4">
      <h1 class="text-lg font-bold">Exam &amp; Training</h1>
      <p class="text-sm opacity-80">ทดสอบความรู้และรับคะแนน EC Passport</p>
    </div>

    <!-- EC Passport Stamps -->
    <div v-if="examBanner" class="mx-4 mt-4">
      <img :src="examBanner.image_url" alt="" class="w-full rounded-lg max-h-36 object-cover">
    </div>

    <div class="card bg-base-200 shadow mx-4 mt-4">
      <div class="card-body py-4">
        <h2 class="card-title text-sm mb-2">EC Passport Card</h2>
        <div class="flex flex-wrap gap-2">
          <div v-for="i in 8" :key="i"
               class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-lg"
               :class="i <= stampCount ? 'border-primary bg-primary text-primary-content' : 'border-base-300 bg-base-100 text-base-300'">
            {{ i <= stampCount ? '⭐' : i }}
          </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ stampCount }}/8 Stamp — ครบ 8 Stamp = {{ stampCount * 10 }} คะแนน</p>
      </div>
    </div>

    <!-- Exam Parts -->
    <div class="px-4 mt-4">
      <div v-if="loading" class="text-center py-8">
        <span class="loading loading-spinner loading-md"></span>
      </div>

      <div v-else-if="!selectedPart">
        <h2 class="font-bold mb-3">รายการ Part</h2>
        <div v-for="part in exams" :key="part.id" class="card bg-base-200 shadow mb-3 cursor-pointer hover:shadow-md transition-shadow"
             @click="openPart(part)">
          <div class="card-body py-4">
            <div class="flex items-center gap-3">
              <div class="relative">
                <img v-if="part.banner_image" :src="'/storage/' + part.banner_image"
                     class="w-16 h-16 rounded-lg object-cover" alt="">
                <div v-else class="w-16 h-16 rounded-lg bg-primary/20 flex items-center justify-center text-2xl">📚</div>
              </div>
              <div class="flex-1">
                <p class="font-bold">{{ part.name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ part.sections_count }} Section</p>
                <div class="flex gap-1 mt-1 flex-wrap">
                  <div v-for="s in part.sections" :key="s"
                       class="badge badge-xs"
                       :class="isCompleted(part.id, s) ? 'badge-success' : 'badge-ghost'">
                    {{ s }}
                  </div>
                </div>
              </div>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Selection -->
      <div v-else-if="selectedPart && !selectedSection">
        <button class="btn btn-ghost btn-sm mb-3 pl-0" @click="selectedPart = null">← กลับ</button>
        <h2 class="font-bold mb-3">{{ selectedPart.name }}</h2>
        <div v-for="section in selectedPart.sections" :key="section"
             class="card bg-base-200 shadow mb-3 cursor-pointer hover:shadow-md"
             @click="startExam(section)">
          <div class="card-body py-4 flex-row items-center justify-between">
            <div>
              <p class="font-semibold">Section {{ section }}</p>
              <div class="badge badge-sm mt-1" :class="isCompleted(selectedPart.id, section) ? 'badge-success' : 'badge-ghost'">
                {{ isCompleted(selectedPart.id, section) ? 'ผ่านแล้ว ✓' : 'ยังไม่ได้ทำ' }}
              </div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Active Quiz -->
      <div v-else-if="questions.length > 0">
        <button class="btn btn-ghost btn-sm mb-3 pl-0" @click="selectedSection = null; questions = []">← กลับ</button>

        <!-- VDO (if any) -->
        <div v-if="selectedPart.vdo_url" class="mb-4">
          <div class="aspect-video rounded-lg overflow-hidden bg-black">
            <iframe :src="selectedPart.vdo_url" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
          </div>
          <p class="text-xs text-gray-500 mt-1 text-center">ดู VDO ก่อนทำแบบทดสอบ</p>
        </div>

        <div class="mb-3 flex items-center justify-between">
          <h2 class="font-bold">{{ selectedPart.name }} — Section {{ selectedSection }}</h2>
          <span class="badge badge-outline">{{ currentQ + 1 }}/{{ questions.length }}</span>
        </div>

        <div v-if="!examDone" class="card bg-base-200 shadow">
          <div class="card-body">
            <p class="font-medium mb-4">{{ currentQ + 1 }}. {{ questions[currentQ].question_text }}</p>
            <div v-for="(choice, idx) in questions[currentQ].choices" :key="idx"
                 class="p-3 rounded-lg border mb-2 cursor-pointer transition-colors"
                 :class="answers[currentQ] === idx ? 'border-primary bg-primary/10' : 'border-base-300 bg-base-100'"
                 @click="selectAnswer(idx)">
              <span class="font-medium mr-2">{{ ['A','B','C','D'][idx] }}.</span>{{ choice }}
            </div>
            <div class="flex justify-between mt-4">
              <button class="btn btn-ghost btn-sm" @click="prevQ" :disabled="currentQ === 0">← ก่อนหน้า</button>
              <button v-if="currentQ < questions.length - 1" class="btn btn-primary btn-sm" @click="nextQ"
                      :disabled="answers[currentQ] === undefined">ถัดไป →</button>
              <button v-else class="btn btn-success btn-sm" @click="submitExam"
                      :disabled="answers.filter(a => a !== undefined).length < questions.length || submitting">
                <span v-if="submitting" class="loading loading-spinner loading-xs"></span>
                ส่งคำตอบ
              </button>
            </div>
          </div>
        </div>

        <!-- Result -->
        <div v-else class="card bg-base-200 shadow text-center">
          <div class="card-body">
            <div class="text-5xl mb-3">{{ examResult.passed ? '🎉' : '😅' }}</div>
            <h3 class="text-xl font-bold">{{ examResult.passed ? 'ผ่านแล้ว!' : 'ยังไม่ผ่าน' }}</h3>
            <p class="text-gray-500 mt-1">คะแนน {{ examResult.score }}/{{ questions.length }}</p>
            <p v-if="examResult.passed" class="text-success font-medium mt-2">+1 Stamp ({{ 10 }} คะแนน)</p>
            <p class="text-xs text-gray-400 mt-1">ต้องได้ {{ examResult.passing_score }}% ขึ้นไป</p>
            <div class="flex gap-2 mt-4">
              <button class="btn btn-ghost flex-1" @click="resetExam">ทำซ้ำ</button>
              <button class="btn btn-primary flex-1" @click="goBack">กลับหน้าหลัก</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <BottomNav />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import BottomNav from '../components/BottomNav.vue';
import api from '../composables/useApi';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(true);
const exams = ref([]);
const examBanner = ref(null);
const myResults = ref([]);
const selectedPart = ref(null);
const selectedSection = ref(null);
const questions = ref([]);
const answers = ref([]);
const currentQ = ref(0);
const examDone = ref(false);
const examResult = ref(null);
const submitting = ref(false);

const stampCount = computed(() => {
    return myResults.value.filter(r => r.passed).length;
});

function isCompleted(partId, section) {
    return myResults.value.some(r => r.exam_part_id === partId && r.section === section && r.passed);
}

onMounted(async () => {
    try {
        const [examRes, bannerRes, resultsRes] = await Promise.all([
            api.get('/api/liff/exams'),
            api.get('/api/liff/banner/exam'),
            api.get('/api/liff/exam-results'),
        ]);
        exams.value = examRes.data;
        examBanner.value = bannerRes.data;
        myResults.value = resultsRes.data;
    } catch (e) {
        // ignore
    } finally {
        loading.value = false;
    }
});

function openPart(part) {
    selectedPart.value = part;
    selectedSection.value = null;
}

async function startExam(section) {
    selectedSection.value = section;
    const { data } = await api.get('/api/liff/exams/' + selectedPart.value.id + '/questions?section=' + section);
    questions.value = data;
    answers.value = new Array(data.length).fill(undefined);
    currentQ.value = 0;
    examDone.value = false;
}

function selectAnswer(idx) {
    answers.value[currentQ.value] = idx;
}

function nextQ() { if (currentQ.value < questions.value.length - 1) currentQ.value++; }
function prevQ() { if (currentQ.value > 0) currentQ.value--; }

async function submitExam() {
    submitting.value = true;
    try {
        const { data } = await api.post('/api/liff/exams/' + selectedPart.value.id + '/submit', {
            section: selectedSection.value,
            answers: answers.value,
        });
        examResult.value = data;
        examDone.value = true;
        if (data.passed) {
            myResults.value.push({ exam_part_id: selectedPart.value.id, section: selectedSection.value, passed: true });
            await authStore.fetchUser();
        }
    } finally {
        submitting.value = false;
    }
}

function resetExam() {
    answers.value = new Array(questions.value.length).fill(undefined);
    currentQ.value = 0;
    examDone.value = false;
    examResult.value = null;
}

function goBack() {
    selectedSection.value = null;
    selectedPart.value = null;
    questions.value = [];
}
</script>
