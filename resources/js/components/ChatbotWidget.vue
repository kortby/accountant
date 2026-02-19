<script setup>
import { ref, nextTick, onMounted, watch } from 'vue';
import axios from 'axios';

const isOpen = ref(false);
const message = ref('');
const messages = ref([]);
const questionsRemaining = ref(5);
const limitReached = ref(false);
const isLoading = ref(false);
const messagesContainer = ref(null);

const STORAGE_KEY = 'chatbot_state';

const loadState = () => {
    try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
            const state = JSON.parse(saved);
            messages.value = state.messages || [];
            questionsRemaining.value = state.questionsRemaining ?? 5;
            limitReached.value = state.limitReached ?? false;
        }
    } catch {
        // Ignore parse errors
    }
};

const saveState = () => {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            messages: messages.value,
            questionsRemaining: questionsRemaining.value,
            limitReached: limitReached.value,
        }));
    } catch {
        // Ignore storage errors
    }
};

const scrollToBottom = async () => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const buildHistory = () => {
    return messages.value
        .filter(m => m.role !== 'system')
        .slice(-10)
        .map(m => ({ role: m.role, content: m.content }));
};

const sendMessage = async () => {
    const text = message.value.trim();
    if (!text || isLoading.value || limitReached.value) {
        return;
    }

    messages.value.push({ role: 'user', content: text });
    message.value = '';
    isLoading.value = true;
    scrollToBottom();

    try {
        const response = await axios.post('/chatbot', {
            message: text,
            history: buildHistory().slice(0, -1),
        });

        const data = response.data;

        if (data.limit_reached && !data.reply) {
            limitReached.value = true;
            questionsRemaining.value = 0;
            messages.value.push({
                role: 'system',
                content: 'limit_reached',
            });
        } else {
            if (data.reply) {
                messages.value.push({ role: 'assistant', content: data.reply });
            }
            questionsRemaining.value = data.questions_remaining;
            limitReached.value = data.limit_reached;

            if (data.limit_reached) {
                messages.value.push({
                    role: 'system',
                    content: 'limit_reached',
                });
            }
        }
    } catch {
        messages.value.push({
            role: 'assistant',
            content: 'Sorry, I\'m having trouble connecting right now. Please call us at (310) 848-8598 or email support@taxesaccountant.co.',
        });
    } finally {
        isLoading.value = false;
        saveState();
        scrollToBottom();
    }
};

const toggleChat = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        scrollToBottom();
    }
};

const handleKeyDown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
};

watch(messages, saveState, { deep: true });

onMounted(() => {
    loadState();
});
</script>

<template>
    <div class="fixed z-[60] bottom-20 right-4 lg:bottom-6 lg:right-6 flex flex-col items-end gap-3">
        <!-- Chat Window -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-4 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-4 scale-95"
        >
            <div
                v-if="isOpen"
                class="w-[340px] sm:w-[380px] h-[500px] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden"
            >
                <!-- Header -->
                <div class="bg-orange-600 px-4 py-3 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-white leading-tight">Tax Assistant</h3>
                            <p class="text-[11px] text-orange-200 leading-tight">Ask us anything</p>
                        </div>
                    </div>
                    <button
                        @click="toggleChat"
                        class="text-white/80 hover:text-white transition-colors p-1"
                        aria-label="Close chat"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Messages -->
                <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-3">
                    <!-- Welcome message -->
                    <div v-if="messages.length === 0" class="space-y-3">
                        <div class="bg-slate-100 rounded-xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%] text-sm text-slate-700">
                            Hi there! I'm the TaxesAccountant virtual assistant. I can help you learn about our services, answer common tax questions, or guide you to book a consultation. How can I help you today?
                        </div>
                    </div>

                    <template v-for="(msg, index) in messages" :key="index">
                        <!-- User message -->
                        <div v-if="msg.role === 'user'" class="flex justify-end">
                            <div class="bg-orange-600 text-white rounded-xl rounded-tr-sm px-3.5 py-2.5 max-w-[85%] text-sm">
                                {{ msg.content }}
                            </div>
                        </div>

                        <!-- Assistant message -->
                        <div v-else-if="msg.role === 'assistant'" class="flex justify-start">
                            <div class="bg-slate-100 text-slate-700 rounded-xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%] text-sm whitespace-pre-line">
                                {{ msg.content }}
                            </div>
                        </div>

                        <!-- Limit reached card -->
                        <div v-else-if="msg.role === 'system' && msg.content === 'limit_reached'" class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                            <p class="text-sm font-medium text-orange-800 mb-2">You've reached the question limit for today.</p>
                            <p class="text-sm text-orange-700 mb-3">For personalized help, our team is ready to assist you:</p>
                            <div class="space-y-2">
                                <a href="tel:+13108488598" class="flex items-center gap-2 text-sm font-medium text-orange-700 hover:text-orange-900 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    (310) 848-8598
                                </a>
                                <a href="mailto:support@taxesaccountant.co" class="flex items-center gap-2 text-sm font-medium text-orange-700 hover:text-orange-900 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    support@taxesaccountant.co
                                </a>
                                <a href="/book" class="mt-2 inline-flex items-center justify-center w-full rounded-lg text-sm font-semibold bg-orange-600 text-white hover:bg-orange-700 h-9 px-4 transition-colors">
                                    Book a Consultation
                                </a>
                            </div>
                        </div>
                    </template>

                    <!-- Typing indicator -->
                    <div v-if="isLoading" class="flex justify-start">
                        <div class="bg-slate-100 rounded-xl rounded-tl-sm px-4 py-3 text-sm text-slate-400">
                            <div class="flex gap-1">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions remaining indicator -->
                <div v-if="!limitReached && messages.length > 0" class="px-4 py-1.5 text-center border-t border-slate-100">
                    <span class="text-xs text-slate-400">{{ questionsRemaining }} question{{ questionsRemaining !== 1 ? 's' : '' }} remaining today</span>
                </div>

                <!-- Input area -->
                <div v-if="!limitReached" class="p-3 border-t border-slate-200 shrink-0">
                    <div class="flex items-center gap-2">
                        <input
                            v-model="message"
                            @keydown="handleKeyDown"
                            type="text"
                            placeholder="Type your question..."
                            maxlength="500"
                            :disabled="isLoading"
                            class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent disabled:opacity-50 placeholder:text-slate-400"
                        />
                        <button
                            @click="sendMessage"
                            :disabled="!message.trim() || isLoading"
                            class="shrink-0 w-9 h-9 inline-flex items-center justify-center rounded-lg bg-orange-600 text-white hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Send message"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Floating Bubble Button -->
        <button
            @click="toggleChat"
            class="w-14 h-14 bg-orange-600 hover:bg-orange-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center group"
            :aria-label="isOpen ? 'Close chat' : 'Open chat'"
        >
            <Transition
                enter-active-class="transition duration-200"
                enter-from-class="rotate-90 opacity-0"
                enter-to-class="rotate-0 opacity-100"
                leave-active-class="transition duration-150"
                leave-from-class="rotate-0 opacity-100"
                leave-to-class="-rotate-90 opacity-0"
                mode="out-in"
            >
                <svg v-if="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </Transition>
        </button>
    </div>
</template>
