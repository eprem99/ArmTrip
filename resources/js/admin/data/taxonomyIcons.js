/**
 * Heroicons (outline 24) — keys stored in DB, used in admin sidebar & taxonomy form.
 */
import {
    AcademicCapIcon,
    BeakerIcon,
    BoltIcon,
    BookmarkIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    CameraIcon,
    ChartBarIcon,
    ClockIcon,
    Cog6ToothIcon,
    DocumentTextIcon,
    FaceSmileIcon,
    FlagIcon,
    FolderIcon,
    GlobeAltIcon,
    HeartIcon,
    HomeIcon,
    LanguageIcon,
    MapPinIcon,
    NewspaperIcon,
    PhotoIcon,
    RectangleStackIcon,
    SparklesIcon,
    Squares2X2Icon,
    TagIcon,
    TruckIcon,
    UsersIcon,
} from '@heroicons/vue/24/outline';

export const taxonomyIconMap = {
    'rectangle-stack': RectangleStackIcon,
    tag: TagIcon,
    folder: FolderIcon,
    'map-pin': MapPinIcon,
    sparkles: SparklesIcon,
    photo: PhotoIcon,
    newspaper: NewspaperIcon,
    bookmark: BookmarkIcon,
    flag: FlagIcon,
    'globe-alt': GlobeAltIcon,
    home: HomeIcon,
    'squares-2x2': Squares2X2Icon,
    users: UsersIcon,
    heart: HeartIcon,
    truck: TruckIcon,
    camera: CameraIcon,
    'calendar-days': CalendarDaysIcon,
    clock: ClockIcon,
    'chart-bar': ChartBarIcon,
    'cog-6-tooth': Cog6ToothIcon,
    'document-text': DocumentTextIcon,
    language: LanguageIcon,
    'building-office-2': BuildingOffice2Icon,
    bolt: BoltIcon,
    'academic-cap': AcademicCapIcon,
    beaker: BeakerIcon,
    'face-smile': FaceSmileIcon,
};

/**
 * Default icon key per taxonomy slug (matches TaxonomySeeder).
 * Used for sidebar + suggested choice in admin form.
 */
export const taxonomySuggestedIconBySlug = {
    category: 'folder',
    location: 'map-pin',
    'content-type': 'document-text',
    duration: 'clock',
    activity: 'bolt',
    tags: 'tag',
};

/** Show these keys first in the icon picker, then the rest A–Z. */
const ICON_OPTION_ORDER = [
    'folder',
    'map-pin',
    'document-text',
    'clock',
    'bolt',
    'tag',
    'sparkles',
    'newspaper',
    'photo',
    'globe-alt',
    'home',
    'bookmark',
    'rectangle-stack',
    'squares-2x2',
    'chart-bar',
    'calendar-days',
    'users',
    'heart',
    'camera',
    'truck',
    'flag',
    'language',
    'cog-6-tooth',
    'building-office-2',
    'academic-cap',
    'beaker',
    'face-smile',
];

function buildTaxonomyIconOptions() {
    const keys = Object.keys(taxonomyIconMap);
    const used = new Set();
    const ordered = [];
    for (const k of ICON_OPTION_ORDER) {
        if (taxonomyIconMap[k]) {
            ordered.push(k);
            used.add(k);
        }
    }
    for (const k of [...keys].sort()) {
        if (!used.has(k)) {
            ordered.push(k);
        }
    }
    return ordered.map((key) => ({ key, component: taxonomyIconMap[key] }));
}

export const taxonomyIconOptions = buildTaxonomyIconOptions();

export function getTaxonomyIconComponent(key) {
    if (!key || typeof key !== 'string') {
        return RectangleStackIcon;
    }
    return taxonomyIconMap[key] || RectangleStackIcon;
}
