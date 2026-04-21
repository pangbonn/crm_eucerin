import provinceData from '@/data/province.json';
import amphureData  from '@/data/amphure.json';
import tambonData   from '@/data/tambon.json';

// Trim ให้เหลือแค่ field ที่ใช้ — ลด memory
const allProvinces = provinceData
    .filter(p => !p.deleted_at)
    .map(p => ({ id: p.id, name: p.name_th }));

const allAmphures = amphureData
    .filter(a => !a.deleted_at)
    .map(a => ({ id: a.id, name: a.name_th, province_id: a.province_id }));

const allTambons = tambonData
    .filter(t => !t.deleted_at)
    .map(t => ({ id: t.id, name: t.name_th, district_id: t.district_id, zip: t.zip_code }));

export function useThaiAddress() {
    // ส่ง full list ให้ AddressTypeahead filter เอง
    function getProvinces() {
        return allProvinces;
    }

    function getAmphuresByProvince(provinceId) {
        if (!provinceId) return [];
        return allAmphures.filter(a => a.province_id === provinceId);
    }

    function getTambonsByAmphure(amphureId) {
        if (!amphureId) return [];
        return allTambons.filter(t => t.district_id === amphureId);
    }

    function getProvinceById(id)  { return allProvinces.find(p => p.id === id) || null; }
    function getAmphureById(id)   { return allAmphures.find(a => a.id === id) || null; }
    function getTambonById(id)    { return allTambons.find(t => t.id === id) || null; }

    return { getProvinces, getAmphuresByProvince, getTambonsByAmphure, getProvinceById, getAmphureById, getTambonById };
}
