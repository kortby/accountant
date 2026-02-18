<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    title: String,
});

const page = usePage();
const mobileMenuOpen = ref(false);
const scrolled = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const handleScroll = () => {
    scrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const navLinks = [
    { href: '/', label: 'Home', match: (url) => url === '/' },
    { href: '/services', label: 'Services', match: (url) => url.startsWith('/services') },
    { href: '/about', label: 'About', match: (url) => url.startsWith('/about') },
    { href: '/contact', label: 'Contact', match: (url) => url.startsWith('/contact') },
];
</script>

<template>
    <div class="min-h-screen bg-white font-sans text-slate-900 flex flex-col">

        <!-- Top utility bar -->
        <div class="hidden lg:block bg-slate-900 text-slate-300 text-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-10">
                <div class="flex items-center gap-6">
                    <a href="tel:+13108488598" class="flex items-center gap-1.5 hover:text-white transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        (310) 848-8598
                    </a>
                    <a href="mailto:support@taxesaccountant.co" class="flex items-center gap-1.5 hover:text-white transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        support@taxesaccountant.co
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mon-Fri 9AM - 6PM PST
                    </span>
                </div>
            </div>
        </div>

        <!-- Main navigation -->
        <nav
            :class="[
                'sticky top-0 z-50 transition-all duration-300',
                scrolled ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-slate-100' : 'bg-white border-b border-slate-100'
            ]"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-18">
                    <div class="flex items-center">
                        <Link href="/" class="shrink-0 flex items-center gap-2">
                            <img src="/img/ta-logo.svg" alt="TaxesAccountant Logo" class="h-11 w-auto" />
                        </Link>
                        <div class="hidden lg:ml-12 lg:flex lg:gap-1">
                            <Link
                                v-for="link in navLinks"
                                :key="link.href"
                                :href="link.href"
                                :class="[
                                    'relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                                    link.match($page.url)
                                        ? 'text-orange-700 bg-orange-50'
                                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'
                                ]"
                            >
                                {{ link.label }}
                            </Link>
                        </div>
                    </div>

                    <div class="hidden lg:flex items-center gap-3">
                        <a href="tel:+13108488598" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors px-3 py-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Call Now (310) 848-8598
                        </a>
                        <Link v-if="$page.props.auth.user" href="/dashboard" class="inline-flex items-center justify-center rounded-lg text-sm font-semibold transition-all duration-200 bg-slate-900 text-white hover:bg-slate-800 h-10 px-5 shadow-sm">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link href="/login" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors px-3 py-2">
                                Sign In
                            </Link>
                            <Link v-if="canRegister" href="/book" class="inline-flex items-center justify-center rounded-lg text-sm font-semibold transition-all duration-200 bg-orange-600 text-white hover:bg-orange-700 h-10 px-5 shadow-sm shadow-orange-600/20">
                                Book Consultation
                            </Link>
                        </template>
                    </div>

                    <div class="-mr-2 flex items-center lg:hidden">
                        <button @click="toggleMobileMenu" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 focus:outline-none transition-colors">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <div v-if="mobileMenuOpen" class="lg:hidden bg-white border-t border-slate-100 shadow-lg">
                    <div class="px-4 pt-4 pb-6 space-y-1">
                        <Link
                            v-for="link in navLinks"
                            :key="link.href"
                            :href="link.href"
                            :class="[
                                'block px-4 py-3 rounded-lg text-base font-medium transition-colors',
                                link.match($page.url)
                                    ? 'text-orange-700 bg-orange-50'
                                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                            ]"
                            @click="mobileMenuOpen = false"
                        >
                            {{ link.label }}
                        </Link>

                        <div class="pt-4 mt-4 border-t border-slate-100 space-y-2">
                            <a href="tel:+13108488598" class="flex items-center gap-2 px-4 py-3 text-base font-medium text-slate-600 hover:bg-slate-50 rounded-lg">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                (310) 848-8598
                            </a>
                            <Link href="/book" class="block w-full text-center rounded-lg text-base font-semibold bg-orange-600 text-white hover:bg-orange-700 px-4 py-3 transition-colors" @click="mobileMenuOpen = false">
                                Book Consultation
                            </Link>
                        </div>
                    </div>
                </div>
            </Transition>
        </nav>

        <main class="grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 mt-auto">
            <!-- Pre-footer CTA -->
            <div class="bg-orange-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold text-white">Ready to simplify your taxes?</h3>
                            <p class="mt-1 text-orange-200 text-sm sm:text-base">Schedule a free consultation with one of our experts today.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link href="/book" class="inline-flex items-center justify-center rounded-lg text-sm font-semibold bg-white text-orange-800 hover:bg-orange-50 h-11 px-6 transition-colors shadow-sm">
                                Book Consultation
                            </Link>
                            <a href="tel:+13108488598" class="inline-flex items-center justify-center gap-2 rounded-lg text-sm font-semibold border border-orange-400 text-white hover:bg-orange-600 h-11 px-6 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                (310) 848-8598
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto py-14 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">
                    <!-- Brand -->
                    <div class="lg:col-span-4">
                        <img src="/img/ta-logo-dark.svg" alt="TaxesAccountant Logo" class="h-11 w-auto" />
                        <p class="mt-4 text-sm text-slate-400 leading-relaxed max-w-sm">
                            Professional tax preparation, bookkeeping, and financial consulting in Lawndale, CA. Trusted by individuals and businesses since 2015.
                        </p>
                        <!-- Certifications -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 border border-slate-700 rounded-full px-3 py-1">
                                <svg class="h-3.5 w-3.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                                IRS Enrolled Agent
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 border border-slate-700 rounded-full px-3 py-1">
                                <svg class="h-3.5 w-3.5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                </svg>
                                256-bit SSL
                            </span>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="lg:col-span-2">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Services</h3>
                        <ul class="mt-4 space-y-3">
                            <li><Link href="/services" class="text-sm text-slate-400 hover:text-white transition-colors">Individual Taxes</Link></li>
                            <li><Link href="/services" class="text-sm text-slate-400 hover:text-white transition-colors">Business Taxes</Link></li>
                            <li><Link href="/services" class="text-sm text-slate-400 hover:text-white transition-colors">Bookkeeping</Link></li>
                            <li><Link href="/services" class="text-sm text-slate-400 hover:text-white transition-colors">Payroll Services</Link></li>
                            <li><Link href="/services" class="text-sm text-slate-400 hover:text-white transition-colors">Tax Planning</Link></li>
                        </ul>
                    </div>

                    <!-- Company -->
                    <div class="lg:col-span-2">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Company</h3>
                        <ul class="mt-4 space-y-3">
                            <li><Link href="/about" class="text-sm text-slate-400 hover:text-white transition-colors">About Us</Link></li>
                            <li><Link href="/contact" class="text-sm text-slate-400 hover:text-white transition-colors">Contact</Link></li>
                            <li><Link href="/blog" class="text-sm text-slate-400 hover:text-white transition-colors">Blog</Link></li>
                            <li><Link href="#" class="text-sm text-slate-400 hover:text-white transition-colors">Privacy Policy</Link></li>
                            <li><Link href="#" class="text-sm text-slate-400 hover:text-white transition-colors">Terms of Service</Link></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div class="lg:col-span-4">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">Contact</h3>
                        <ul class="mt-4 space-y-3 text-sm">
                            <li class="flex items-start gap-3 text-slate-400">
                                <svg class="h-5 w-5 mt-0.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>4446 Manhattan Beach Blvd<br />Lawndale, CA 90260</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="h-5 w-5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <a href="tel:+13108488598" class="text-slate-400 hover:text-white transition-colors">+1 (310) 848-8598</a>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="h-5 w-5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <a href="mailto:support@taxesaccountant.co" class="text-slate-400 hover:text-white transition-colors">support@taxesaccountant.co</a>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="h-5 w-5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-slate-400">Mon - Fri: 9:00 AM - 6:00 PM PST</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">&copy; {{ new Date().getFullYear() }} TaxesAccountant. All rights reserved.</p>
                    <p class="text-xs text-slate-600">Licensed Tax Professionals &middot; Lawndale, California</p>
                </div>
            </div>
        </footer>

        <!-- Mobile sticky CTA bar -->
        <div class="fixed bottom-0 inset-x-0 z-50 lg:hidden bg-white border-t border-slate-200 shadow-[0_-2px_10px_rgba(0,0,0,0.08)] safe-area-bottom">
            <div class="flex items-center gap-2 px-4 py-3">
                <a href="tel:+13108488598" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg text-sm font-semibold border border-slate-300 text-slate-700 hover:bg-slate-50 h-11 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Call
                </a>
                <Link href="/book" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg text-sm font-semibold bg-orange-600 text-white hover:bg-orange-700 h-11 transition-colors shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Book Now
                </Link>
            </div>
        </div>

        <!-- Spacer for mobile sticky bar -->
        <div class="h-17 lg:hidden"></div>
    </div>
</template>

<style scoped>
.safe-area-bottom {
    padding-bottom: env(safe-area-inset-bottom, 0px);
}
</style>