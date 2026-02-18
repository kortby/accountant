<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/layouts/MainLayout.vue';
import { onMounted, ref } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    latestBlogs: Array,
    testimonials: Array,
});

const openFaqIndex = ref(null);
const toggleFaq = (index) => {
    openFaqIndex.value = openFaqIndex.value === index ? null : index;
};

const services = [
    {
        title: 'Individual Tax Preparation',
        description: 'Comprehensive personal tax filing for W-2 employees, freelancers, and investors. We maximize every deduction you qualify for.',
        icon: 'user',
    },
    {
        title: 'Business Tax Services',
        description: 'Full-service tax compliance for LLCs, S-Corps, partnerships, and sole proprietors. Strategic filing that reduces your tax liability.',
        icon: 'briefcase',
    },
    {
        title: 'Bookkeeping',
        description: 'Year-round financial record keeping so your books are always audit-ready. Monthly reconciliation and clear financial reporting.',
        icon: 'book',
    },
    {
        title: 'Payroll Services',
        description: 'Accurate payroll processing, tax withholding, and quarterly filings. Keep your team paid on time, every time.',
        icon: 'wallet',
    },
    {
        title: 'Tax Planning',
        description: 'Proactive strategies throughout the year to minimize your tax burden. Don\'t just file — plan ahead and save more.',
        icon: 'chart',
    },
    {
        title: 'Financial Consulting',
        description: 'Expert guidance on business structure, retirement planning, and financial growth strategies tailored to your goals.',
        icon: 'lightbulb',
    },
];

const processSteps = [
    {
        step: '01',
        title: 'Schedule a Consultation',
        description: 'Book a free consultation online or by phone. We\'ll discuss your situation and recommend the best approach.',
    },
    {
        step: '02',
        title: 'Provide Your Documents',
        description: 'Securely upload your W-2s, 1099s, receipts, and other documents through our encrypted portal.',
    },
    {
        step: '03',
        title: 'We Handle Everything',
        description: 'Our experts prepare, review, and optimize your return. You\'ll receive a detailed summary before we file.',
    },
    {
        step: '04',
        title: 'Ongoing Support',
        description: 'Year-round access to your accountant for questions, planning, and any IRS correspondence.',
    },
];

const faqs = [
    {
        id: 1,
        question: 'Is my personal and financial data secure?',
        answer: 'Absolutely. We use bank-level 256-bit SSL encryption for all data transmission and storage. Your documents are stored on secure, private servers, and we strictly adhere to IRS data security standards to ensure your information never falls into the wrong hands.',
    },
    {
        id: 2,
        question: 'How much does it cost to file my taxes?',
        answer: 'We believe in transparent pricing. Our base rate for individual returns starts at a fixed price, with adjustments only for complex schedules (like rental properties or business income). You will always see the estimated cost before we file — no hidden fees or surprises.',
    },
    {
        id: 3,
        question: 'How long does the process take?',
        answer: 'Once you upload your documents, most returns are prepared for your review within 2-3 business days. During peak season (April), this may extend slightly, but we provide real-time status updates on your dashboard so you always know where your return stands.',
    },
    {
        id: 4,
        question: 'What if I get audited by the IRS?',
        answer: 'We stand by our work. If you receive an audit notice for a return we prepared, we offer full audit representation services. Our enrolled agents will deal directly with the IRS on your behalf to resolve the issue.',
    },
    {
        id: 5,
        question: 'Do I need to visit an office in person?',
        answer: 'No. Our entire process is 100% digital. You can upload documents, sign forms electronically, and communicate with your accountant through our secure portal from the comfort of your home.',
    },
    {
        id: 6,
        question: 'Can I talk to a real accountant if I have questions?',
        answer: 'Yes! While our platform is digital, our service is human. You can message your assigned tax expert directly through the platform or schedule a video call if you need to discuss complex tax situations.',
    },
];

const getInitials = (author) => {
    if (!author) return 'A';
    if (author.first_name && author.last_name) {
        return (author.first_name.charAt(0) + author.last_name.charAt(0)).toUpperCase();
    }
    if (author.name) {
        const parts = author.name.split(' ').filter(Boolean);
        if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
        return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
    }
    return 'A';
};

onMounted(() => {
    const els = Array.from(document.querySelectorAll('.scroll-animate'));
    if (!els.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = el.dataset.delay ? parseInt(el.dataset.delay, 10) : 0;
                setTimeout(() => el.classList.add('in-view'), delay);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.1 });

    els.forEach((el, idx) => {
        if (!el.dataset.delay) el.dataset.delay = String(idx * 70);
        observer.observe(el);
    });
});
</script>

<template>
    <Head title="Professional Tax & Accounting Services in Lawndale, CA" />

    <MainLayout :can-login="canLogin" :can-register="canRegister">

        <!-- ==================== HERO SECTION ==================== -->
        <section class="relative bg-slate-900 overflow-hidden">
            <!-- Ambient gradient blobs -->
            <div class="hero-glow-1 absolute -top-40 -left-40 w-125 h-125 bg-orange-600/15 rounded-full blur-3xl"></div>
            <div class="hero-glow-2 absolute -bottom-32 right-0 w-100 h-100 bg-amber-500/10 rounded-full blur-3xl"></div>
            <div class="hero-glow-3 absolute top-1/3 left-1/2 w-75 h-75 bg-orange-500/8 rounded-full blur-3xl"></div>

            <!-- Subtle grid overlay -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,.02)_1px,transparent_1px)] bg-size-[64px_64px]"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 items-center py-16 sm:py-20 lg:py-28">
                    <!-- Left content — takes 7 cols -->
                    <div class="lg:col-span-7 text-center lg:text-left">
                        <!-- Status badge -->
                        <div class="inline-flex items-center gap-2.5 rounded-full bg-white/6 backdrop-blur-sm border border-white/10 px-4 py-2 mb-8 scroll-animate" data-delay="0">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-400"></span>
                            </span>
                            <span class="text-sm font-medium text-slate-300">Now accepting new clients for <span class="text-orange-400 font-semibold">2025 tax season</span></span>
                        </div>

                        <h1 class="scroll-animate" data-delay="80">
                            <span class="block text-[2.5rem] sm:text-5xl lg:text-[3.5rem] xl:text-[3.75rem] font-extrabold tracking-tight text-white leading-[1.08]">
                                Your Trusted
                            </span>
                            <span class="block text-[2.5rem] sm:text-5xl lg:text-[3.5rem] xl:text-[3.75rem] font-extrabold tracking-tight leading-[1.08] mt-1">
                                <span class="hero-gradient-text">Tax &amp; Accounting</span>
                            </span>
                            <span class="block text-[2.5rem] sm:text-5xl lg:text-[3.5rem] xl:text-[3.75rem] font-extrabold tracking-tight text-white leading-[1.08] mt-1">
                                Partner in Lawndale
                            </span>
                        </h1>

                        <p class="mt-6 text-lg text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed scroll-animate" data-delay="160">
                            Expert tax preparation, bookkeeping, and financial consulting for individuals and businesses in
                            <strong class="text-slate-300 font-medium">Lawndale</strong> and the greater
                            <strong class="text-slate-300 font-medium">Los Angeles</strong> area.
                        </p>

                        <!-- CTAs -->
                        <div class="mt-8 flex flex-col sm:flex-row gap-3.5 justify-center lg:justify-start scroll-animate" data-delay="240">
                            <Link href="/book" class="group inline-flex items-center justify-center gap-2.5 rounded-xl text-base font-semibold bg-orange-600 text-white hover:bg-orange-500 h-13 px-8 transition-all duration-300 shadow-lg shadow-orange-600/25 hover:shadow-orange-500/35 hover:-translate-y-0.5">
                                <svg class="h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Book Free Consultation
                            </Link>
                            <a href="tel:+13108488598" class="group inline-flex items-center justify-center gap-2.5 rounded-xl text-base font-semibold border border-slate-600/80 text-white hover:border-slate-500 hover:bg-white/5 h-13 px-8 transition-all duration-300 backdrop-blur-sm">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-40"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-orange-400"></span>
                                </span>
                                (310) 848-8598
                            </a>
                        </div>

                        <!-- Social proof row -->
                        <div class="mt-10 scroll-animate" data-delay="320">
                            <div class="flex flex-col sm:flex-row items-center lg:items-start gap-6 sm:gap-8">
                                <!-- Star rating -->
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-0.5">
                                        <svg v-for="i in 5" :key="i" class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                    <div class="text-sm">
                                        <span class="font-bold text-white">4.9/5</span>
                                        <span class="text-slate-500 ml-1">from 120+ reviews</span>
                                    </div>
                                </div>

                                <span class="hidden sm:block w-px h-8 bg-slate-700"></span>

                                <!-- Trust badges -->
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1.5 text-sm text-slate-400">
                                        <svg class="h-4.5 w-4.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                        IRS Enrolled Agent
                                    </div>
                                    <div class="flex items-center gap-1.5 text-sm text-slate-400">
                                        <svg class="h-4.5 w-4.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                        </svg>
                                        SSL Encrypted
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right side — takes 5 cols -->
                    <div class="lg:col-span-5 scroll-animate" data-delay="200">
                        <div class="relative">
                            <!-- Background glow (desktop) -->
                            <div class="hidden lg:block absolute -inset-8 bg-linear-to-tr from-orange-600/20 via-transparent to-amber-500/10 rounded-3xl blur-2xl"></div>

                            <!-- Accounting illustration -->
                            <div class="relative mx-auto max-w-xs sm:max-w-sm lg:max-w-none">
                                <svg viewBox="0 0 440 500" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" aria-label="Financial dashboard illustration">
                                    <!-- Soft background circles -->
                                    <circle cx="220" cy="250" r="210" fill="url(#heroIllBg)" opacity="0.06"/>
                                    <circle cx="220" cy="250" r="145" fill="url(#heroIllBg)" opacity="0.04"/>

                                    <!-- Main dashboard screen -->
                                    <rect x="60" y="50" width="320" height="400" rx="20" fill="#1e293b" stroke="#334155" stroke-width="1"/>
                                    <!-- Screen bezel highlight -->
                                    <rect x="60" y="50" width="320" height="4" rx="2" fill="#334155" opacity="0.5"/>
                                    <!-- Inner screen -->
                                    <rect x="76" y="76" width="288" height="354" rx="12" fill="#0f172a"/>

                                    <!-- Dashboard header -->
                                    <rect x="92" y="92" width="72" height="6" rx="3" fill="#475569"/>
                                    <rect x="92" y="104" width="48" height="4" rx="2" fill="#334155"/>
                                    <!-- Status dot -->
                                    <circle cx="344" cy="97" r="5" fill="#f97316" opacity="0.2"/>
                                    <circle cx="344" cy="97" r="3" fill="#f97316"/>

                                    <!-- Featured metric -->
                                    <text x="92" y="146" font-family="system-ui, -apple-system, sans-serif" font-size="30" font-weight="700" fill="#f97316">$850K+</text>
                                    <text x="92" y="163" font-family="system-ui, -apple-system, sans-serif" font-size="9" fill="#64748b" letter-spacing="0.08em">TOTAL CLIENT SAVINGS</text>

                                    <!-- Mini metric cards -->
                                    <rect x="92" y="178" width="84" height="44" rx="8" fill="#1e293b"/>
                                    <rect x="184" y="178" width="84" height="44" rx="8" fill="#1e293b"/>
                                    <rect x="276" y="178" width="72" height="44" rx="8" fill="#1e293b"/>
                                    <!-- Card 1 -->
                                    <text x="102" y="200" font-family="system-ui, sans-serif" font-size="15" font-weight="700" fill="white">500+</text>
                                    <text x="102" y="214" font-family="system-ui, sans-serif" font-size="8" fill="#64748b">Returns</text>
                                    <!-- Card 2 -->
                                    <text x="194" y="200" font-family="system-ui, sans-serif" font-size="15" font-weight="700" fill="white">99%</text>
                                    <text x="194" y="214" font-family="system-ui, sans-serif" font-size="8" fill="#64748b">Satisfaction</text>
                                    <!-- Card 3 -->
                                    <text x="286" y="200" font-family="system-ui, sans-serif" font-size="15" font-weight="700" fill="#f97316">10Y+</text>
                                    <text x="286" y="214" font-family="system-ui, sans-serif" font-size="8" fill="#64748b">Experience</text>

                                    <!-- Line chart -->
                                    <path d="M92 276 C128 272, 152 258, 178 250 S224 240, 256 226 S296 212, 348 196" stroke="#f97316" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                    <!-- Area fill -->
                                    <path d="M92 276 C128 272, 152 258, 178 250 S224 240, 256 226 S296 212, 348 196 V296 H92 Z" fill="url(#heroChartFill)" opacity="0.12"/>
                                    <!-- Data points -->
                                    <circle cx="178" cy="250" r="4" fill="#f97316"/>
                                    <circle cx="256" cy="226" r="4" fill="#f97316"/>
                                    <circle cx="348" cy="196" r="4" fill="#f97316"/>
                                    <!-- Subtle grid -->
                                    <line x1="92" y1="256" x2="348" y2="256" stroke="#1e293b" stroke-width="0.5"/>
                                    <line x1="92" y1="276" x2="348" y2="276" stroke="#1e293b" stroke-width="0.5"/>
                                    <line x1="92" y1="296" x2="348" y2="296" stroke="#1e293b" stroke-width="0.5"/>

                                    <!-- Bar chart -->
                                    <rect x="102" y="342" width="30" height="65" rx="4" fill="#f97316" opacity="0.2"/>
                                    <rect x="142" y="318" width="30" height="89" rx="4" fill="#f97316" opacity="0.35"/>
                                    <rect x="182" y="295" width="30" height="112" rx="4" fill="#f97316" opacity="0.5"/>
                                    <rect x="222" y="326" width="30" height="81" rx="4" fill="#f97316" opacity="0.65"/>
                                    <rect x="262" y="290" width="30" height="117" rx="4" fill="#f97316" opacity="0.8"/>
                                    <rect x="302" y="262" width="30" height="145" rx="4" fill="#f97316"/>
                                    <!-- Baseline -->
                                    <line x1="92" y1="407" x2="348" y2="407" stroke="#334155" stroke-width="0.5"/>

                                    <!-- Floating calculator -->
                                    <g transform="translate(340, 24) rotate(6)">
                                        <rect width="96" height="126" rx="12" fill="white" filter="url(#heroCardShadow)"/>
                                        <!-- Screen -->
                                        <rect x="10" y="10" width="76" height="26" rx="6" fill="#f8fafc"/>
                                        <text x="76" y="30" text-anchor="end" font-family="system-ui, sans-serif" font-size="15" font-weight="700" fill="#0f172a">1,247</text>
                                        <!-- Operator row -->
                                        <rect x="10" y="44" width="15" height="15" rx="4" fill="#f97316"/>
                                        <rect x="29" y="44" width="15" height="15" rx="4" fill="#fed7aa"/>
                                        <rect x="48" y="44" width="15" height="15" rx="4" fill="#fed7aa"/>
                                        <rect x="67" y="44" width="15" height="15" rx="4" fill="#fef3c7"/>
                                        <!-- Number rows -->
                                        <rect x="10" y="64" width="15" height="15" rx="4" fill="#f1f5f9"/>
                                        <rect x="29" y="64" width="15" height="15" rx="4" fill="#f1f5f9"/>
                                        <rect x="48" y="64" width="15" height="15" rx="4" fill="#f1f5f9"/>
                                        <rect x="67" y="64" width="15" height="15" rx="4" fill="#f1f5f9"/>
                                        <rect x="10" y="84" width="15" height="15" rx="4" fill="#f1f5f9"/>
                                        <rect x="29" y="84" width="15" height="15" rx="4" fill="#f1f5f9"/>
                                        <rect x="48" y="84" width="15" height="15" rx="4" fill="#f1f5f9"/>
                                        <rect x="67" y="84" width="15" height="15" rx="4" fill="#f97316"/>
                                        <!-- Bottom row -->
                                        <rect x="10" y="104" width="34" height="14" rx="4" fill="#f1f5f9"/>
                                        <rect x="48" y="104" width="34" height="14" rx="4" fill="#f97316"/>
                                    </g>

                                    <!-- Floating receipt / document -->
                                    <g transform="translate(6, 330) rotate(-5)">
                                        <rect width="82" height="106" rx="10" fill="white" filter="url(#heroCardShadow)"/>
                                        <!-- Text lines -->
                                        <rect x="10" y="12" width="62" height="3" rx="1.5" fill="#e2e8f0"/>
                                        <rect x="10" y="20" width="46" height="3" rx="1.5" fill="#e2e8f0"/>
                                        <rect x="10" y="28" width="54" height="3" rx="1.5" fill="#e2e8f0"/>
                                        <rect x="10" y="40" width="62" height="3" rx="1.5" fill="#e2e8f0"/>
                                        <rect x="10" y="48" width="34" height="3" rx="1.5" fill="#e2e8f0"/>
                                        <!-- Divider -->
                                        <line x1="10" y1="60" x2="72" y2="60" stroke="#f1f5f9" stroke-width="1"/>
                                        <!-- Total amount -->
                                        <rect x="10" y="68" width="26" height="3" rx="1.5" fill="#94a3b8"/>
                                        <text x="72" y="74" text-anchor="end" font-family="system-ui, sans-serif" font-size="11" font-weight="700" fill="#f97316">$2,480</text>
                                        <!-- Checkmark -->
                                        <circle cx="41" cy="92" r="9" fill="#fff7ed"/>
                                        <path d="M35 92 L39 96 L47 88" stroke="#f97316" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                    </g>

                                    <!-- Floating dollar coin -->
                                    <circle cx="42" cy="88" r="22" fill="#f97316" filter="url(#heroCardShadow)"/>
                                    <circle cx="42" cy="88" r="16" fill="none" stroke="white" stroke-width="1.5" opacity="0.3"/>
                                    <text x="42" y="96" text-anchor="middle" font-family="system-ui, sans-serif" font-size="20" font-weight="700" fill="white">$</text>

                                    <!-- Decorative dots -->
                                    <circle cx="395" cy="440" r="3" fill="#f97316" opacity="0.3"/>
                                    <circle cx="410" cy="425" r="2" fill="#fb923c" opacity="0.2"/>
                                    <circle cx="25" cy="200" r="2.5" fill="#f97316" opacity="0.25"/>

                                    <!-- Definitions -->
                                    <defs>
                                        <linearGradient id="heroIllBg" x1="0" y1="0" x2="1" y2="1">
                                            <stop offset="0%" stop-color="#f97316"/>
                                            <stop offset="100%" stop-color="#fbbf24"/>
                                        </linearGradient>
                                        <linearGradient id="heroChartFill" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#f97316"/>
                                            <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
                                        </linearGradient>
                                        <filter id="heroCardShadow" x="-20%" y="-15%" width="140%" height="140%">
                                            <feDropShadow dx="0" dy="4" stdDeviation="8" flood-color="#000" flood-opacity="0.18"/>
                                        </filter>
                                    </defs>
                                </svg>
                            </div>

                            <!-- Floating stat cards (desktop only) -->
                            <div class="hero-float-card hidden lg:block absolute -left-10 top-10 bg-white/95 backdrop-blur-sm rounded-xl shadow-xl border border-white/50 px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-slate-900 leading-none">500+</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Returns Filed</div>
                                    </div>
                                </div>
                            </div>

                            <div class="hero-float-card-delayed hidden lg:block absolute -right-6 bottom-28 bg-white/95 backdrop-blur-sm rounded-xl shadow-xl border border-white/50 px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-slate-900 leading-none">$850K+</div>
                                        <div class="text-xs text-slate-500 mt-0.5">Saved for Clients</div>
                                    </div>
                                </div>
                            </div>

                            <div class="hero-float-card hidden lg:block absolute -left-4 bottom-14 bg-white/95 backdrop-blur-sm rounded-xl shadow-xl border border-white/50 px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-slate-900 leading-none">10+ Years</div>
                                        <div class="text-xs text-slate-500 mt-0.5">of Experience</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom gradient bleed into next section -->
            <div class="absolute inset-x-0 bottom-0 h-px bg-linear-to-r from-transparent via-orange-500/30 to-transparent"></div>
        </section>

        <!-- ==================== STATS BAR ==================== -->
        <section class="relative -mt-1 bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center scroll-animate" data-delay="0">
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900">10+</div>
                        <div class="mt-1 text-sm text-slate-500 font-medium">Years of Experience</div>
                    </div>
                    <div class="text-center scroll-animate" data-delay="80">
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900">500+</div>
                        <div class="mt-1 text-sm text-slate-500 font-medium">Returns Filed</div>
                    </div>
                    <div class="text-center scroll-animate" data-delay="160">
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900">99%</div>
                        <div class="mt-1 text-sm text-slate-500 font-medium">Client Satisfaction</div>
                    </div>
                    <div class="text-center scroll-animate" data-delay="240">
                        <div class="text-3xl sm:text-4xl font-bold text-slate-900">$850K+</div>
                        <div class="mt-1 text-sm text-slate-500 font-medium">Saved for Clients</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== SERVICES SECTION ==================== -->
        <section class="bg-slate-50 py-20 sm:py-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center scroll-animate" data-delay="0">
                    <span class="text-sm font-semibold tracking-widest uppercase text-orange-600">What We Offer</span>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        Comprehensive Financial Services
                    </h2>
                    <p class="mt-4 text-lg text-slate-500 leading-relaxed">
                        From individual tax returns to full business accounting, we provide everything you need under one roof.
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div
                        v-for="(service, idx) in services"
                        :key="service.title"
                        class="group bg-white rounded-xl p-7 border border-slate-200/80 hover:border-orange-200 hover:shadow-lg hover:shadow-orange-50 transition-all duration-300 scroll-animate"
                        :data-delay="String(idx * 80)"
                    >
                        <!-- Icon -->
                        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center mb-5 group-hover:bg-orange-100 transition-colors">
                            <!-- User -->
                            <svg v-if="service.icon === 'user'" class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <!-- Briefcase -->
                            <svg v-else-if="service.icon === 'briefcase'" class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            <!-- Book -->
                            <svg v-else-if="service.icon === 'book'" class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                            <!-- Wallet -->
                            <svg v-else-if="service.icon === 'wallet'" class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                            </svg>
                            <!-- Chart -->
                            <svg v-else-if="service.icon === 'chart'" class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            <!-- Lightbulb -->
                            <svg v-else class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                            </svg>
                        </div>

                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ service.title }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ service.description }}</p>
                    </div>
                </div>

                <div class="mt-12 text-center scroll-animate" data-delay="200">
                    <Link href="/services" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                        View all services
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ==================== TRUST & AUTHORITY SECTION ==================== -->
        <section class="bg-white py-20 sm:py-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Left - Image / About -->
                    <div class="scroll-animate" data-delay="0">
                        <div class="relative">
                            <div class="absolute -inset-4 bg-linear-to-br from-orange-100 to-amber-50 rounded-2xl"></div>
                            <div class="relative rounded-2xl overflow-hidden shadow-lg">
                                <img src="/img/photos/about.png" class="w-full h-auto object-cover" alt="Our accounting team" />
                            </div>
                            <!-- Floating badge -->
                            <div class="absolute -bottom-6 -right-4 sm:-right-6 bg-white rounded-xl shadow-lg border border-slate-100 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-full bg-orange-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-orange-700" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">Licensed &amp; Insured</div>
                                        <div class="text-xs text-slate-500">IRS Enrolled Agent</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right - Content -->
                    <div>
                        <span class="text-sm font-semibold tracking-widest uppercase text-orange-600 scroll-animate" data-delay="80">About Our Firm</span>
                        <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 scroll-animate" data-delay="120">
                            Driven by Accuracy,<br /> Defined by Integrity
                        </h2>
                        <p class="mt-5 text-lg text-slate-500 leading-relaxed scroll-animate" data-delay="160">
                            Founded with a simple mission — to make tax filing stress-free for everyone. With over 10 years of experience, our team of Enrolled Agents and CPAs has helped thousands of individuals and small businesses navigate complex tax laws with confidence.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-start gap-3 scroll-animate" data-delay="200">
                                <div class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center">
                                    <svg class="h-3.5 w-3.5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">Certified experts</span>
                                    <span class="text-sm text-slate-500"> reviewing every return before filing</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 scroll-animate" data-delay="240">
                                <div class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center">
                                    <svg class="h-3.5 w-3.5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">100% digital process</span>
                                    <span class="text-sm text-slate-500"> — file from the comfort of home</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 scroll-animate" data-delay="280">
                                <div class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center">
                                    <svg class="h-3.5 w-3.5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">Full audit representation</span>
                                    <span class="text-sm text-slate-500"> if the IRS contacts you</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 scroll-animate" data-delay="320">
                                <div class="mt-0.5 shrink-0 w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center">
                                    <svg class="h-3.5 w-3.5 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-slate-900">Transparent pricing</span>
                                    <span class="text-sm text-slate-500"> with no hidden fees or surprises</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 scroll-animate" data-delay="360">
                            <Link href="/about" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                                Learn more about us
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== PROCESS SECTION ==================== -->
        <section class="bg-slate-900 py-20 sm:py-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center scroll-animate" data-delay="0">
                    <span class="text-sm font-semibold tracking-widest uppercase text-orange-400">How It Works</span>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-white">
                        A Simple, Streamlined Process
                    </h2>
                    <p class="mt-4 text-lg text-slate-400 leading-relaxed">
                        We've made professional tax preparation as easy as four simple steps.
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div
                        v-for="(step, idx) in processSteps"
                        :key="step.step"
                        class="relative scroll-animate"
                        :data-delay="String(idx * 100)"
                    >
                        <!-- Connector line (hidden on last and mobile) -->
                        <div v-if="idx < processSteps.length - 1" class="hidden lg:block absolute top-8 left-[calc(50%+2rem)] w-[calc(100%-4rem)] h-px bg-slate-700"></div>

                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-600/10 border border-orange-500/20 mb-5">
                                <span class="text-xl font-bold text-orange-400">{{ step.step }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-white mb-2">{{ step.title }}</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">{{ step.description }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-14 text-center scroll-animate" data-delay="400">
                    <Link href="/book" class="inline-flex items-center justify-center gap-2 rounded-lg text-base font-semibold bg-orange-600 text-white hover:bg-orange-500 h-12 px-7 transition-all duration-200 shadow-lg shadow-orange-600/25">
                        Get Started Today
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ==================== TESTIMONIALS SECTION ==================== -->
        <section v-if="testimonials && testimonials.length" class="bg-slate-50 py-20 sm:py-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center scroll-animate" data-delay="0">
                    <span class="text-sm font-semibold tracking-widest uppercase text-orange-600">Testimonials</span>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        What Our Clients Say
                    </h2>
                    <p class="mt-4 text-lg text-slate-500">
                        Real stories from real clients who trust us with their finances.
                    </p>
                </div>

                <div class="mt-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div
                        v-for="(item, idx) in testimonials.slice(0, 6)"
                        :key="item.id"
                        class="bg-white rounded-xl p-7 border border-slate-200/80 shadow-sm scroll-animate"
                        :data-delay="String(idx * 80)"
                    >
                        <!-- Rating -->
                        <div v-if="item.rating" class="flex items-center gap-0.5 mb-4">
                            <svg
                                v-for="star in 5"
                                :key="star"
                                class="w-4.5 h-4.5"
                                :class="star <= item.rating ? 'text-amber-400' : 'text-slate-200'"
                                :fill="star <= item.rating ? 'currentColor' : 'none'"
                                stroke="currentColor"
                                stroke-width="1.5"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                        </div>

                        <blockquote class="text-slate-600 text-sm leading-relaxed">
                            "{{ item.content }}"
                        </blockquote>

                        <div class="mt-5 flex items-center gap-3 pt-5 border-t border-slate-100">
                            <div class="w-10 h-10 rounded-full bg-linear-to-br from-orange-200 to-amber-100 flex items-center justify-center text-orange-800 font-bold text-sm">
                                {{ item.user ? item.author_name.charAt(0) : 'C' }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ item.user ? item.author_name : 'Client' }}
                                </div>
                                <div v-if="item.author_title || item.author_company" class="text-xs text-slate-500">
                                    <span v-if="item.author_title">{{ item.author_title }}</span>
                                    <span v-if="item.author_title && item.author_company"> at </span>
                                    <span v-if="item.author_company">{{ item.author_company }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== FAQ SECTION ==================== -->
        <section class="bg-white py-20 sm:py-28">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center scroll-animate" data-delay="0">
                    <span class="text-sm font-semibold tracking-widest uppercase text-orange-600">FAQ</span>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                        Frequently Asked Questions
                    </h2>
                    <p class="mt-4 text-lg text-slate-500">
                        Common questions about our tax and accounting services.
                    </p>
                </div>

                <div class="mt-14 divide-y divide-slate-200">
                    <div
                        v-for="(faq, idx) in faqs"
                        :key="faq.id"
                        class="scroll-animate"
                        :data-delay="String(idx * 60)"
                    >
                        <button
                            @click="toggleFaq(idx)"
                            class="w-full flex items-center justify-between py-6 text-left group"
                        >
                            <span class="text-base font-semibold text-slate-900 pr-8 group-hover:text-orange-600 transition-colors">
                                {{ faq.question }}
                            </span>
                            <span class="shrink-0 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-orange-50 transition-colors">
                                <svg
                                    class="h-4 w-4 text-slate-500 transition-transform duration-200"
                                    :class="{ 'rotate-180': openFaqIndex === idx }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </button>
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="max-h-0 opacity-0"
                            enter-to-class="max-h-96 opacity-100"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="max-h-96 opacity-100"
                            leave-to-class="max-h-0 opacity-0"
                        >
                            <div v-if="openFaqIndex === idx" class="overflow-hidden">
                                <p class="pb-6 text-slate-500 leading-relaxed pr-12">
                                    {{ faq.answer }}
                                </p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==================== BLOG SECTION ==================== -->
        <section v-if="latestBlogs && latestBlogs.length" class="bg-slate-50 py-20 sm:py-28 border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between gap-4 scroll-animate" data-delay="0">
                    <div>
                        <span class="text-sm font-semibold tracking-widest uppercase text-orange-600">Our Blog</span>
                        <h2 class="mt-2 text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                            Latest Tax Tips &amp; Insights
                        </h2>
                    </div>
                    <Link href="/blog" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors shrink-0">
                        View all posts
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>

                <div class="mt-12 grid gap-8 lg:grid-cols-3">
                    <article
                        v-for="(post, idx) in latestBlogs"
                        :key="post.id"
                        class="group bg-white rounded-xl overflow-hidden border border-slate-200/80 hover:shadow-lg hover:shadow-slate-100 transition-all duration-300 scroll-animate"
                        :data-delay="String(idx * 100)"
                    >
                        <div class="aspect-16/10 overflow-hidden">
                            <img class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500" :src="post.imageUrl" :alt="post.title" />
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 text-sm mb-3">
                                <span class="font-medium text-orange-600">{{ post.category.title }}</span>
                                <span class="text-slate-300">&middot;</span>
                                <time :datetime="post.datetime" class="text-slate-500">{{ post.date }}</time>
                            </div>
                            <Link :href="post.href" class="block group/link">
                                <h3 class="text-lg font-semibold text-slate-900 group-hover/link:text-orange-600 transition-colors line-clamp-2">
                                    {{ post.title }}
                                </h3>
                                <p class="mt-2 text-sm text-slate-500 leading-relaxed line-clamp-3">
                                    {{ post.description }}
                                </p>
                            </Link>
                            <div class="mt-5 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-linear-to-br from-orange-200 to-amber-100 flex items-center justify-center text-orange-800 font-bold text-xs">
                                    {{ getInitials(post.author) }}
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ post.author.name }}</span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

    </MainLayout>
</template>

<style scoped>
/* === Scroll animation === */
.scroll-animate {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    will-change: transform, opacity;
}
.scroll-animate.in-view {
    opacity: 1;
    transform: translateY(0);
}
.scroll-animate[data-delay="0"] {
    transition-duration: 0.8s;
}

/* === Hero gradient text === */
.hero-gradient-text {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 40%, #fed7aa 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* === Ambient glow animations === */
.hero-glow-1 {
    animation: glow-drift-1 12s ease-in-out infinite;
}
.hero-glow-2 {
    animation: glow-drift-2 14s ease-in-out infinite;
}
.hero-glow-3 {
    animation: glow-drift-3 16s ease-in-out infinite;
}

@keyframes glow-drift-1 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, 20px) scale(1.05); }
    66% { transform: translate(-20px, 10px) scale(0.98); }
}
@keyframes glow-drift-2 {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-25px, -15px) scale(1.03); }
    66% { transform: translate(15px, -25px) scale(0.97); }
}
@keyframes glow-drift-3 {
    0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
    50% { transform: translate(-30px, 20px) scale(1.1); opacity: 0.8; }
}

/* === Floating card animations === */
.hero-float-card {
    animation: float-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.6s both,
               gentle-bob 5s ease-in-out 1.4s infinite;
}
.hero-float-card-delayed {
    animation: float-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.9s both,
               gentle-bob 6s ease-in-out 1.7s infinite;
}

@keyframes float-up {
    from {
        opacity: 0;
        transform: translateY(24px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
@keyframes gentle-bob {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

/* === Reduced motion === */
@media (prefers-reduced-motion: reduce) {
    .scroll-animate {
        transition: none !important;
        transform: none !important;
        opacity: 1 !important;
    }
    .hero-glow-1, .hero-glow-2, .hero-glow-3,
    .hero-float-card, .hero-float-card-delayed {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }
}
</style>
