<script setup>
    import {
        Head,
        Link
    } from '@inertiajs/vue3';
    import MainLayout from '@/Layouts/MainLayout.vue';

    defineProps({
        canLogin: Boolean,
        canRegister: Boolean,
        latestBlogs: Array,
        testimonials: Array,
    });

    const faqs = [{
            id: 1,
            question: "Is my personal and financial data secure?",
            answer: "Absolutely. We use bank-level 256-bit SSL encryption for all data transmission and storage. Your documents are stored on secure, private servers, and we strictly adhere to IRS data security standards to ensure your information never falls into the wrong hands.",
        },
        {
            id: 2,
            question: 'How much does it cost to file my taxes?',
            answer: 'We believe in transparent pricing. Our base rate for individual returns starts at a fixed price, with adjustments only for complex schedules (like rental properties or business income). You will always see the estimated cost before we file—no hidden fees or surprises.',
        },
        {
            id: 3,
            question: 'How long does the process take?',
            answer: 'Once you upload your documents, most returns are prepared for your review within 2-3 business days. During peak season (April), this may extend slightly, but we provide real-time status updates on your dashboard so you always know where your return stands.',
        },
        {
            id: 4,
            question: 'What if I get audited by the IRS?',
            answer: "We stand by our work. If you receive an audit notice for a return we prepared, we offer full audit representation services. Our enrolled agents will deal directly with the IRS on your behalf to resolve the issue.",
        },
        {
            id: 5,
            question: "Do I need to visit an office in person?",
            answer: 'No. Our entire process is 100% digital. You can upload documents, sign forms electronically, and communicate with your accountant through our secure portal from the comfort of your home.',
        },
        {
            id: 6,
            question: 'Can I talk to a real accountant if I have questions?',
            answer: "Yes! While our platform is digital, our service is human. You can message your assigned tax expert directly through the platform or schedule a video call if you need to discuss complex tax situations.",
        },
    ]
</script>

<template>

    <Head title="Home - Professional Tax Filing" />

    <MainLayout :can-login="canLogin" :can-register="canRegister">

        <div class="relative bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                    <svg class="hidden lg:block absolute right-0 inset-y-0 h-full w-48 text-white transform translate-x-1/2"
                        fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        <polygon points="50,0 100,0 50,100 0,100" />
                    </svg>

                    <div class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                        <div class="sm:text-center lg:text-left">
                            <h1
                                class="text-4xl tracking-tight font-extrabold sm:text-5xl md:text-6xl bg-clip-text text-transparent bg-gradient-to-r from-orange-700 to-orange-400">
                                <span class="block xl:inline">Professional Tax Filing</span>
                                <span class="block ">Made Easy</span>
                            </h1>
                            <p
                                class="mt-3 text-base text-slate-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                                Let our expert accountants handle your taxes while you focus on what matters most.
                                Secure, efficient, and personalized tax services for individuals and businesses.
                            </p>
                            <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start gap-3">
                                <div class="rounded-md shadow">
                                    <Link v-if="canRegister" href="/register"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 md:py-4 md:text-lg transition-all">
                                    Get started
                                    </Link>
                                    <Link v-else href="/dashboard"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 md:py-4 md:text-lg transition-all">
                                    Go to Dashboard
                                    </Link>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <Link href="/book"
                                        class="w-full flex items-center justify-center px-8 py-3 border border-slate-300 text-base font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 md:py-4 md:text-lg transition-all">
                                    Book appointment
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-slate-100 flex items-center justify-center">
                <div
                    class="w-full h-56 sm:h-72 md:h-96 lg:w-full lg:h-full bg-slate-200 flex items-center justify-center text-slate-400">
                    <img src="img/hero_section.png" class="w-full h-full object-cover" alt="Hero Image">
                </div>
            </div>
        </div>

        <div class="py-12 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base text-orange-600 font-semibold tracking-wide uppercase">Why Choose Us</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                        A better way to file taxes
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-slate-500 lg:mx-auto">
                        We combine modern technology with human expertise to ensure your returns are accurate and
                        optimized.
                    </p>
                </div>

                <div class="mt-10">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                        <div class="relative bg-white p-6 rounded-lg shadow-sm border border-slate-100">
                            <div
                                class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-slate-900">Secure Documents</p>
                            <p class="mt-2 ml-16 text-base text-slate-500">
                                Bank-level encryption for all your uploaded tax documents and personal information.
                            </p>
                        </div>
                        <div class="relative bg-white p-6 rounded-lg shadow-sm border border-slate-100">
                            <div
                                class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.131A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.2-2.858.567-4.168" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-slate-900">Audit Protection</p>
                            <p class="mt-2 ml-16 text-base text-slate-500">
                                Peace of mind knowing a licensed professional reviews your return before filing.
                            </p>
                        </div>
                        <div class="relative bg-white p-6 rounded-lg shadow-sm border border-slate-100">
                            <div
                                class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <p class="ml-16 text-lg leading-6 font-medium text-slate-900">Expert Support</p>
                            <p class="mt-2 ml-16 text-base text-slate-500">
                                Direct access to your accountant via our secure messaging platform.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section v-if="testimonials && testimonials.length" class="py-12 bg-white border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-slate-900 text-center mb-8">Client Success Stories</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="item in testimonials" :key="item.id"
                        class="bg-slate-50 p-6 rounded-xl border border-slate-100 shadow-sm">
                        <p class="text-slate-600 italic">"{{ item . content }}"</p>
                        <div class="mt-4 flex items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="h-10 w-10 rounded-full bg-orange-200 flex items-center justify-center text-orange-700 font-bold">
                                    {{ item . user ? item . user . name . charAt(0) : 'C' }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-slate-900">
                                    {{ item . user ? item . user . name : 'Client' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="bg-gray-900 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base/7 font-semibold text-orange-400">Stress-Free Filing</h2>
                    <p
                        class="mt-2 text-4xl font-semibold tracking-tight text-pretty text-white sm:text-5xl lg:text-balance">
                        Everything you need to file with confidence</p>
                    <p class="mt-6 text-lg/8 text-gray-300">Stop worrying about deadlines and complex deductions. We
                        provide a seamless, secure, and professional tax experience tailored to your specific needs.</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">

                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base/7 font-semibold text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="size-5 flex-none text-orange-400">
                                    <path
                                        d="M5.5 17a4.5 4.5 0 0 1-1.44-8.765 4.5 4.5 0 0 1 8.302-3.046 3.5 3.5 0 0 1 4.504 4.272A4 4 0 0 1 15 17H5.5Zm3.75-2.75a.75.75 0 0 0 1.5 0V9.66l1.95 2.1a.75.75 0 1 0 1.1-1.02l-3.25-3.5a.75.75 0 0 0-1.1 0l-3.25 3.5a.75.75 0 1 0 1.1 1.02l1.95-2.1v4.59Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Easy Document Upload
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base/7 text-gray-400">
                                <p class="flex-auto">Skip the office visit. Securely upload your W-2s, 1099s, and
                                    receipts directly from your phone or computer in seconds.</p>
                                <p class="mt-6">
                                    <a href="#"
                                        class="text-sm/6 font-semibold text-orange-400 hover:text-orange-300">Start
                                        uploading <span aria-hidden="true">→</span></a>
                                </p>
                            </dd>
                        </div>

                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base/7 font-semibold text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="size-5 flex-none text-orange-400">
                                    <path
                                        d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Bank-Level Security
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base/7 text-gray-400">
                                <p class="flex-auto">Your financial data is sensitive. We use 256-bit encryption and
                                    secure servers to ensure your personal information remains private and protected.
                                </p>
                                <p class="mt-6">
                                    <a href="#"
                                        class="text-sm/6 font-semibold text-orange-400 hover:text-orange-300">View
                                        security specs <span aria-hidden="true">→</span></a>
                                </p>
                            </dd>
                        </div>

                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base/7 font-semibold text-white">
                                <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true"
                                    class="size-5 flex-none text-orange-400">
                                    <path
                                        d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                                        clip-rule="evenodd" fill-rule="evenodd" />
                                </svg>
                                Real-Time Tracking
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base/7 text-gray-400">
                                <p class="flex-auto">Never wonder about the status of your return. Track every step
                                    from document preparation to review and final IRS acceptance via our dashboard.</p>
                                <p class="mt-6">
                                    <a href="#"
                                        class="text-sm/6 font-semibold text-orange-400 hover:text-orange-300">See how
                                        it works <span aria-hidden="true">→</span></a>
                                </p>
                            </dd>
                        </div>

                    </dl>
                </div>
            </div>
        </div>


        <div class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8 lg:py-40">
                <h2 class="text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">Frequently asked questions
                </h2>
                <dl class="mt-20 divide-y divide-slate-900/10">
                    <div v-for="faq in faqs" :key="faq.id"
                        class="py-8 first:pt-0 last:pb-0 lg:grid lg:grid-cols-12 lg:gap-8">
                        <dt class="text-base/7 font-semibold text-slate-900 lg:col-span-5">{{ faq . question }}</dt>
                        <dd class="mt-4 lg:col-span-7 lg:mt-0">
                            <p class="text-base/7 text-slate-600">{{ faq . answer }}</p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <section v-if="latestBlogs && latestBlogs.length" class="py-12 bg-slate-50 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl tracking-tight font-extrabold text-slate-900 sm:text-4xl">Latest from the blog
                    </h2>
                    <p class="mt-3 max-w-2xl mx-auto text-xl text-slate-500 sm:mt-4">
                        Tax tips, financial news, and updates.
                    </p>
                </div>
                <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
                    <div v-for="post in latestBlogs" :key="post.id"
                        class="flex flex-col rounded-lg shadow-sm overflow-hidden border border-slate-200 bg-white hover:shadow-md transition-shadow">
                        <div class="flex-shrink-0">
                            <img class="h-48 w-full object-cover" :src="post.imageUrl" :alt="post.title">
                        </div>
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-orange-600">
                                    {{ post . category . title }}
                                </p>
                                <Link :href="post.href" class="block mt-2">
                                <p class="text-xl font-semibold text-slate-900">{{ post . title }}</p>
                                <p class="mt-3 text-base text-slate-500 line-clamp-3">{{ post . description }}</p>
                                </Link>
                            </div>
                            <div class="mt-6 flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" :src="post.author.imageUrl"
                                        :alt="post.author.name">
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-slate-900">
                                        {{ post . author . name }}
                                    </p>
                                    <div class="flex space-x-1 text-sm text-slate-500">
                                        <time :datetime="post.datetime">{{ post . date }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </MainLayout>
</template>
