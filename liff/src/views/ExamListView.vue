<template>
  <div class="pb-20">

    <!-- ===== PART LIST ===== -->
    <div v-if="currentView === 'list'">
      <div class="bg-red-600 text-white py-4 px-4 mb-4 flex items-center gap-3">
        <button class="text-white opacity-80 hover:opacity-100" @click="router.push('/exam')">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <h1 class="font-bold text-lg">รายการแบบทดสอบ</h1>
      </div>

      <div v-if="loading" class="text-center py-8">
        <span class="loading loading-spinner loading-md"></span>
      </div>

      <div v-else class="px-4 space-y-4">
        <div v-for="part in exams" :key="part.id" class="card bg-base-200 shadow">
          <!-- Part Header -->
          <div class="card-body py-3 pb-1">
            <div class="flex items-center gap-3">
              <img v-if="part.banner_image" :src="part.banner_image"
                   class="w-14 h-14 rounded-lg object-cover flex-shrink-0" alt="">
              <div v-else class="w-14 h-14 rounded-lg bg-primary/20 flex items-center justify-center text-2xl flex-shrink-0">📚</div>
              <div>
                <p class="font-bold text-sm">{{ part.name }}</p>
                <p class="text-xs text-gray-400">Part {{ part.part_number }}</p>
              </div>
            </div>
          </div>

          <!-- Completed state -->
          <div v-if="isPartComplete(part)" class="px-4 py-5 text-center border-t border-base-300">
            <div class="text-3xl mb-1">🎉</div>
            <p class="font-bold text-success text-sm">ทำครบทุกรายการแล้ว</p>
            <p class="text-xs text-gray-400 mt-0.5">pre-test · วิดีโอ · post-test</p>
          </div>

          <!-- 3 Rows -->
          <div v-else class="divide-y divide-base-300">

            <!-- 1. Pre-test — เปิดได้เสมอ -->
            <div class="flex items-center justify-between px-4 py-3 cursor-pointer hover:bg-base-300 transition-colors"
                 @click="openExam(part, 'pre')">
              <div class="flex items-center gap-3">
                <span class="text-lg">📝</span>
                <div>
                  <p class="text-sm font-medium">แบบทดสอบก่อนเรียน</p>
                  <p class="text-xs text-gray-500">{{ part.pre.points }} คะแนน</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span v-if="part.pre.passed" class="badge badge-success badge-sm">ผ่านแล้ว ✓</span>
                <span v-else class="badge badge-ghost badge-sm">ยังไม่ทำ</span>
                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>

            <!-- 2. Video — ต้องผ่าน pre ก่อน -->
            <div class="flex items-center justify-between px-4 py-3 transition-colors"
                 :class="part.pre.passed && part.vdo.url ? 'cursor-pointer hover:bg-base-300' : 'opacity-40 cursor-not-allowed'"
                 @click="part.pre.passed && part.vdo.url ? openVideo(part) : showLockToast('ต้องทำแบบทดสอบก่อนเรียนให้ผ่านก่อน')">
              <div class="flex items-center gap-3">
                <span class="text-lg">{{ part.pre.passed ? '🎬' : '🔒' }}</span>
                <div>
                  <p class="text-sm font-medium">วิดีโอ</p>
                  <p class="text-xs text-gray-500">{{ part.pre.passed ? 'ต้องดู 60% ขึ้นไป' : 'ทำ Pre-test ก่อน' }}</p>
                  <div v-if="part.vdo.percentage > 0" class="w-24 mt-1">
                    <progress class="progress progress-primary h-1.5 w-full"
                              :value="part.vdo.percentage" max="100"></progress>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span v-if="part.vdo.passed" class="badge badge-success badge-sm">ดูแล้ว ✓</span>
                <span v-else-if="part.vdo.percentage > 0" class="badge badge-warning badge-sm">{{ Math.round(part.vdo.percentage) }}%</span>
                <span v-else-if="!part.pre.passed" class="badge badge-ghost badge-sm">🔒</span>
                <span v-else-if="part.vdo.url" class="badge badge-ghost badge-sm">ยังไม่ดู</span>
                <span v-else class="badge badge-ghost badge-sm">ไม่มีวิดีโอ</span>
                <svg v-if="part.pre.passed && part.vdo.url" class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>

            <!-- 3. Post-test — ต้องดู video ก่อน (ถ้ามี video) หรือผ่าน pre (ถ้าไม่มี video) -->
            <div class="flex items-center justify-between px-4 py-3 transition-colors rounded-b-2xl"
                 :class="canDoPost(part) ? 'cursor-pointer hover:bg-base-300' : 'opacity-40 cursor-not-allowed'"
                 @click="canDoPost(part) ? openExam(part, 'post') : showLockToast(postLockMsg(part))">
              <div class="flex items-center gap-3">
                <span class="text-lg">{{ canDoPost(part) ? '📝' : '🔒' }}</span>
                <div>
                  <p class="text-sm font-medium">แบบทดสอบหลังเรียน</p>
                  <p class="text-xs text-gray-500">
                    {{ canDoPost(part) ? part.post.points + ' คะแนน' : postLockMsg(part) }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span v-if="part.post.passed" class="badge badge-success badge-sm">ผ่านแล้ว ✓</span>
                <span v-else-if="!canDoPost(part)" class="badge badge-ghost badge-sm">🔒</span>
                <span v-else class="badge badge-ghost badge-sm">ยังไม่ทำ</span>
                <svg v-if="canDoPost(part)" class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>

          </div><!-- /v-else 3 rows -->
        </div>
      </div>
    </div>

    <!-- Stamp Popup -->
    <div v-if="showStampPopup" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 px-4">
      <div class="card bg-base-100 shadow-xl w-full max-w-sm text-center">
        <div class="card-body items-center">
          <div class="text-6xl mb-2">🏅</div>
          <h3 class="text-xl font-bold">รับ Stamp</h3>
          <p class="text-4xl font-bold text-primary my-1">1 ดวง</p>
          <p class="text-gray-500 text-sm mb-4">ดูวิดีโอครบ 100% แล้ว</p>
          <button class="btn btn-primary w-full" @click="goToPostTest">ถัดไป → ทำแบบทดสอบหลังเรียน</button>
        </div>
      </div>
    </div>

    <!-- Toast แจ้งเตือน lock -->
    <div v-if="lockToast"
         class="fixed bottom-24 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-4 py-2 rounded-full shadow-lg z-50 transition-opacity">
      {{ lockToast }}
    </div>

    <!-- ===== VIDEO VIEW ===== -->
    <div v-else-if="currentView === 'video'" class="px-4 mt-2">
      <button class="btn btn-ghost btn-sm pl-0 mb-3" @click="goBackToList">← กลับ</button>
      <h2 class="font-bold mb-3">{{ selectedPart.name }} — วิดีโอ</h2>

      <div v-if="isDirectVideo(selectedPart.vdo.url)" class="rounded-xl overflow-hidden bg-black mb-3">
        <video :src="selectedPart.vdo.url" controls class="w-full"
               controlsList="nodownload noplaybackrate nofullscreen"
               @contextmenu.prevent
               @timeupdate="onVideoTimeUpdate"
               @ended="onVideoEnded"
               playsinline></video>
      </div>
      <div v-else class="aspect-video rounded-xl overflow-hidden bg-black mb-3">
        <iframe :src="selectedPart.vdo.url" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
      </div>

      <div class="card bg-base-200 mb-3">
        <div class="card-body py-3">
          <div class="flex items-center justify-between mb-1">
            <p class="text-sm font-medium">ความคืบหน้า</p>
            <span class="text-sm font-bold" :class="videoWatched >= 100 ? 'text-success' : 'text-gray-500'">
              {{ Math.round(videoWatched) }}%
            </span>
          </div>
          <progress class="progress progress-primary w-full" :value="videoWatched" max="100"></progress>
          <p class="text-xs text-gray-400 mt-1">ต้องดูจบ 100% เพื่อรับ Stamp</p>

          <div v-if="videoCompleted" class="alert alert-success py-2 mt-2">
            <span class="text-sm">✓ บันทึกการดูแล้ว</span>
          </div>
          <button v-else-if="!isDirectVideo(selectedPart.vdo.url)"
                  class="btn btn-primary btn-sm w-full mt-2"
                  :disabled="savingVideo"
                  @click="markVideoWatched">
            <span v-if="savingVideo" class="loading loading-spinner loading-xs"></span>
            ยืนยันดูวิดีโอครบแล้ว
          </button>
        </div>
      </div>
    </div>

    <!-- ===== EXAM VIEW ===== -->
    <div v-else-if="currentView === 'exam'" class="px-4 mt-2">
      <button class="btn btn-ghost btn-sm pl-0 mb-3" @click="goBackToList">← กลับ</button>

      <!-- Already Passed -->
      <div v-if="alreadyPassed" class="card bg-base-200 shadow text-center">
        <div class="card-body">
          <div class="text-5xl mb-3">🎉</div>
          <h3 class="text-xl font-bold">ผ่านแล้ว!</h3>
          <p class="text-gray-500 mt-1">คุณทำแบบทดสอบนี้ผ่านแล้ว</p>
          <button class="btn btn-ghost mt-4" @click="goBackToList">กลับ</button>
        </div>
      </div>

      <div v-else-if="loadingQuestions" class="text-center py-8">
        <span class="loading loading-spinner loading-md"></span>
      </div>

      <div v-else-if="!examDone && questions.length > 0">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="font-bold text-sm">
            {{ selectedPart.name }} —
            {{ selectedSection === 'pre' ? 'ก่อนเรียน' : 'หลังเรียน' }}
          </h2>
          <span class="badge badge-outline">{{ currentQ + 1 }}/{{ questions.length }}</span>
        </div>

        <div class="card bg-base-200 shadow">
          <div class="card-body">
            <p class="font-medium mb-4">{{ currentQ + 1 }}. {{ questions[currentQ].question_text }}</p>
            <div v-for="(choice, idx) in questions[currentQ].choices" :key="idx"
                 class="p-3 rounded-lg border mb-2 cursor-pointer transition-colors"
                 :class="answers[questions[currentQ].id] === idx ? 'border-primary bg-primary/10' : 'border-base-300 bg-base-100'"
                 @click="selectAnswer(idx)">
              <span class="font-medium mr-2">{{ ['A','B','C','D'][idx] }}.</span>{{ choice }}
            </div>
            <div class="flex justify-between mt-4">
              <button class="btn btn-ghost btn-sm" @click="prevQ" :disabled="currentQ === 0">← ก่อนหน้า</button>
              <button v-if="currentQ < questions.length - 1"
                      class="btn btn-primary btn-sm" @click="nextQ"
                      :disabled="answers[questions[currentQ].id] === undefined">ถัดไป →</button>
              <button v-else class="btn btn-success btn-sm" @click="submitExam"
                      :disabled="Object.keys(answers).length < questions.length || submitting">
                <span v-if="submitting" class="loading loading-spinner loading-xs"></span>
                ส่งคำตอบ
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="examDone" class="card bg-base-200 shadow text-center">
        <div class="card-body">
          <div class="text-5xl mb-3">{{ examResult.passed ? '🎉' : '😅' }}</div>
          <h3 class="text-xl font-bold">{{ examResult.passed ? 'ผ่านแล้ว!' : 'ยังไม่ผ่าน' }}</h3>
          <p class="text-gray-500 mt-1">{{ examResult.score }}/{{ examResult.max_score }} ข้อ ({{ examResult.percentage }}%)</p>
          <p v-if="examResult.passed && examResult.points_earned > 0" class="text-success font-medium mt-2">
            +{{ examResult.points_earned }} คะแนน
          </p>
          <p class="text-xs text-gray-400 mt-1">ต้องได้ {{ selectedSection === 'pre' ? '10' : '70' }}% ขึ้นไป</p>
          <div class="flex gap-2 mt-4">
            <button v-if="!examResult.passed && selectedSection === 'pre'" class="btn btn-ghost flex-1" @click="retakeExam">ทำซ้ำ</button>
            <button class="btn btn-primary flex-1" @click="goBackToList">เสร็จสิ้น</button>
          </div>
        </div>
      </div>
    </div>

  </div>
  <BottomNav />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import BottomNav from '@/components/BottomNav.vue';
import api from '@/composables/useApi';

const router    = useRouter();
const authStore = useAuthStore();
const loading   = ref(true);
const lockToast = ref(null);
const exams     = ref([]);

const currentView      = ref('list');
const selectedPart     = ref(null);
const selectedSection  = ref(null);

const questions        = ref([]);
const answers          = ref({});  // {question_id: choice_index}
const currentQ         = ref(0);
const examDone         = ref(false);
const examResult       = ref(null);
const submitting       = ref(false);
const loadingQuestions = ref(false);
const alreadyPassed    = ref(false);

const videoWatched   = ref(0);
const videoCompleted = ref(false);
const savingVideo    = ref(false);
const showStampPopup = ref(false);
let videoProgressSent = false;
let lastVideoTime = 0;

onMounted(async () => {
    try {
        const { data } = await api.get('/api/liff/exams');
        exams.value = data;
    } catch (e) {
        // ignore
    } finally {
        loading.value = false;
    }
});

function isDirectVideo(url) {
    if (!url) return false;
    return !['youtube.com', 'youtu.be', 'vimeo.com'].some(h => url.includes(h));
}

function goBackToList() {
    currentView.value     = 'list';
    selectedPart.value    = null;
    selectedSection.value = null;
    questions.value       = [];
    examDone.value        = false;
    examResult.value      = null;
    alreadyPassed.value   = false;
}

async function openExam(part, section) {
    selectedPart.value     = part;
    selectedSection.value  = section;
    currentView.value      = 'exam';
    alreadyPassed.value    = false;
    examDone.value         = false;
    examResult.value       = null;
    loadingQuestions.value = true;
    try {
        const { data } = await api.get('/api/liff/exams/' + part.id + '/questions?section=' + section);
        questions.value = data;
        answers.value   = {};
        currentQ.value  = 0;
    } catch (e) {
        if (e.response && e.response.status === 403) alreadyPassed.value = true;
    } finally {
        loadingQuestions.value = false;
    }
}

function openVideo(part) {
    selectedPart.value   = part;
    currentView.value    = 'video';
    videoWatched.value   = part.vdo.percentage;
    videoCompleted.value = part.vdo.passed;
    videoProgressSent    = part.vdo.passed;
    lastVideoTime        = 0;
    showStampPopup.value = false;
}

function onVideoTimeUpdate(event) {
    const video = event.target;
    if (!video.duration) return;
    // Prevent forward seeking
    if (video.currentTime > lastVideoTime + 0.5) {
        video.currentTime = lastVideoTime;
        return;
    }
    lastVideoTime = video.currentTime;
    const pct = Math.round((video.currentTime / video.duration) * 100);
    videoWatched.value = pct;
}

function onVideoEnded() {
    if (!videoProgressSent) sendVideoProgress(100);
}

async function sendVideoProgress(percentage) {
    if (videoProgressSent || savingVideo.value) return;
    videoProgressSent = true;
    savingVideo.value = true;
    try {
        await api.post('/api/liff/exams/' + selectedPart.value.id + '/video-progress', { percentage });
        videoCompleted.value = true;
        const part = exams.value.find(p => p.id === selectedPart.value.id);
        if (part) { part.vdo.percentage = percentage; part.vdo.passed = true; }
        showStampPopup.value = true;
    } catch (e) {
        videoProgressSent = false;
    } finally {
        savingVideo.value = false;
    }
}

async function markVideoWatched() { await sendVideoProgress(100); }

function selectAnswer(idx) {
    answers.value = Object.assign({}, answers.value, { [questions.value[currentQ.value].id]: idx });
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
        examDone.value   = true;
        if (data.passed) {
            const part = exams.value.find(p => p.id === selectedPart.value.id);
            if (part && selectedSection.value === 'pre')  part.pre.passed  = true;
            if (part && selectedSection.value === 'post') part.post.passed = true;
            await authStore.fetchUser();
        }
    } catch (e) {
        if (e.response && e.response.status === 403) alreadyPassed.value = true;
    } finally {
        submitting.value = false;
    }
}

function retakeExam() {
    answers.value    = {};
    currentQ.value   = 0;
    examDone.value   = false;
    examResult.value = null;
}

function isPartComplete(part) {
    return part.pre.passed && part.post.passed && (!part.vdo.url || part.vdo.passed);
}

function goToPostTest() {
    showStampPopup.value = false;
    openExam(selectedPart.value, 'post');
}

function canDoPost(part) {
    if (part.vdo.url) return part.vdo.passed;
    return part.pre.passed;
}

function postLockMsg(part) {
    if (!part.pre.passed) return 'ต้องทำแบบทดสอบก่อนเรียนให้ผ่านก่อน';
    if (part.vdo.url && !part.vdo.passed) return 'ต้องดูวิดีโอให้ครบ 60% ก่อน';
    return '';
}

let lockToastTimer = null;
function showLockToast(msg) {
    lockToast.value = msg;
    if (lockToastTimer) clearTimeout(lockToastTimer);
    lockToastTimer = setTimeout(() => { lockToast.value = null; }, 2500);
}
</script>
