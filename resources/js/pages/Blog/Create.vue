<script setup>
import { ref, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Checkbox } from '@/components/ui/checkbox';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Underline from '@tiptap/extension-underline';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    tags: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    title: '',
    content: '',
    excerpt: '',
    category_id: null,
    featured_image: null,
    tags: [],
    status: 'draft',
});

const imagePreview = ref(null);

// Initialize TipTap editor
const editor = useEditor({
    extensions: [
        StarterKit,
        Underline,
        Link.configure({
            openOnClick: false,
        }),
        Image,
    ],
    content: form.content,
    editorProps: {
        attributes: {
            class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-xl mx-auto focus:outline-none min-h-[300px] p-4',
        },
    },
    onUpdate: ({ editor }) => {
        form.content = editor.getHTML();
    },
});

// Watch for content changes
watch(() => form.content, (newContent) => {
    if (editor.value && editor.value.getHTML() !== newContent) {
        editor.value.commands.setContent(newContent, false);
    }
});

const handleImageSelect = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.featured_image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.featured_image = null;
    imagePreview.value = null;
};

const toggleTag = (tagId) => {
    const index = form.tags.indexOf(tagId);
    if (index > -1) {
        form.tags.splice(index, 1);
    } else {
        form.tags.push(tagId);
    }
};

const submit = () => {
    form.post('/blogs', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        },
    });
};

const saveDraft = () => {
    form.status = 'draft';
    submit();
};

const publish = () => {
    form.status = 'published';
    submit();
};

const setLink = () => {
    const url = window.prompt('Enter URL:');
    if (url) {
        editor.value.chain().focus().setLink({ href: url }).run();
    }
};

const addImage = () => {
    const url = window.prompt('Enter image URL:');
    if (url) {
        editor.value.chain().focus().setImage({ src: url }).run();
    }
};
</script>

<template>
    <Head title="Create Blog Post" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Create New Blog Post
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit">
                    <div class="space-y-6">
                        
                        <!-- Title -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Post Title</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-2">
                                    <Label for="title">Title *</Label>
                                    <Input
                                        id="title"
                                        v-model="form.title"
                                        placeholder="Enter your blog post title..."
                                        :class="{ 'border-red-500': form.errors.title }"
                                    />
                                    <p v-if="form.errors.title" class="text-sm text-red-600">
                                        {{ form.errors.title }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Excerpt -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Excerpt</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-2">
                                    <Label for="excerpt">Short Description *</Label>
                                    <Textarea
                                        id="excerpt"
                                        v-model="form.excerpt"
                                        placeholder="Write a brief summary of your blog post (max 500 characters)..."
                                        rows="3"
                                        maxlength="500"
                                        :class="{ 'border-red-500': form.errors.excerpt }"
                                    />
                                    <div class="flex justify-between text-sm">
                                        <p v-if="form.errors.excerpt" class="text-red-600">
                                            {{ form.errors.excerpt }}
                                        </p>
                                        <p class="text-gray-500 ml-auto">
                                            {{ form.excerpt.length }}/500
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Content -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Content</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-2">
                                    <Label for="content">Blog Content *</Label>
                                    
                                    <!-- Editor Toolbar -->
                                    <div v-if="editor" class="border rounded-t-lg p-2 bg-gray-50 dark:bg-gray-800 flex flex-wrap gap-1">
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleBold().run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('bold') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 font-bold"
                                            title="Bold"
                                        >
                                            B
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleItalic().run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('italic') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 italic"
                                            title="Italic"
                                        >
                                            I
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleUnderline().run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('underline') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 underline"
                                            title="Underline"
                                        >
                                            U
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleStrike().run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('strike') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 line-through"
                                            title="Strikethrough"
                                        >
                                            S
                                        </button>
                                        <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('heading', { level: 1 }) }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Heading 1"
                                        >
                                            H1
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('heading', { level: 2 }) }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Heading 2"
                                        >
                                            H2
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('heading', { level: 3 }) }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Heading 3"
                                        >
                                            H3
                                        </button>
                                        <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleBulletList().run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('bulletList') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Bullet List"
                                        >
                                            • List
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleOrderedList().run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('orderedList') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Numbered List"
                                        >
                                            1. List
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().toggleBlockquote().run()"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('blockquote') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Quote"
                                        >
                                            "
                                        </button>
                                        <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                        <button
                                            type="button"
                                            @click="setLink"
                                            :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('link') }"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Add Link"
                                        >
                                            🔗
                                        </button>
                                        <button
                                            type="button"
                                            @click="addImage"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Add Image"
                                        >
                                            🖼️
                                        </button>
                                        <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().setHorizontalRule().run()"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Horizontal Rule"
                                        >
                                            ―
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().undo().run()"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Undo"
                                        >
                                            ↶
                                        </button>
                                        <button
                                            type="button"
                                            @click="editor.chain().focus().redo().run()"
                                            class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Redo"
                                        >
                                            ↷
                                        </button>
                                    </div>
                                    
                                    <!-- Editor Content -->
                                    <div 
                                        class="border rounded-b-lg bg-white dark:bg-gray-900 min-h-[400px]"
                                        :class="{ 'border-red-500': form.errors.content }"
                                    >
                                        <EditorContent :editor="editor" />
                                    </div>
                                    
                                    <p v-if="form.errors.content" class="text-sm text-red-600">
                                        {{ form.errors.content }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Category and Tags -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Category & Tags</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-6">
                                <!-- Category -->
                                <div class="space-y-2">
                                    <Label for="category">Category *</Label>
                                    <Select v-model="form.category_id">
                                        <SelectTrigger :class="{ 'border-red-500': form.errors.category_id }">
                                            <SelectValue placeholder="Select a category" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="category in categories"
                                                :key="category.id"
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <p v-if="form.errors.category_id" class="text-sm text-red-600">
                                        {{ form.errors.category_id }}
                                    </p>
                                </div>

                                <!-- Tags -->
                                <div class="space-y-2">
                                    <Label>Tags (Optional)</Label>
                                    <div class="flex flex-wrap gap-3">
                                        <div
                                            v-for="tag in tags"
                                            :key="tag.id"
                                            class="flex items-center space-x-2"
                                        >
                                            <Checkbox
                                                :id="`tag-${tag.id}`"
                                                :checked="form.tags.includes(tag.id)"
                                                @update:checked="toggleTag(tag.id)"
                                            />
                                            <label
                                                :for="`tag-${tag.id}`"
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 cursor-pointer"
                                            >
                                                {{ tag.name }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Featured Image -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Featured Image</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-4">
                                    <div v-if="!imagePreview">
                                        <Label for="featured_image">Upload Image (Optional)</Label>
                                        <Input
                                            id="featured_image"
                                            type="file"
                                            accept="image/*"
                                            @change="handleImageSelect"
                                            class="mt-2"
                                        />
                                        <p class="text-xs text-gray-500 mt-1">
                                            Maximum file size: 2MB. Accepted formats: JPG, PNG, WebP
                                        </p>
                                    </div>

                                    <div v-else class="relative">
                                        <img
                                            :src="imagePreview"
                                            alt="Preview"
                                            class="w-full h-64 object-cover rounded-lg"
                                        />
                                        <Button
                                            type="button"
                                            @click="removeImage"
                                            variant="destructive"
                                            size="sm"
                                            class="absolute top-2 right-2"
                                        >
                                            Remove
                                        </Button>
                                    </div>

                                    <p v-if="form.errors.featured_image" class="text-sm text-red-600">
                                        {{ form.errors.featured_image }}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Actions -->
                        <Card>
                            <CardContent class="pt-6">
                                <div class="flex justify-between items-center">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        as="a"
                                        href="/dashboard"
                                    >
                                        Cancel
                                    </Button>

                                    <div class="flex gap-3">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            @click="saveDraft"
                                            :disabled="form.processing"
                                        >
                                            Save as Draft
                                        </Button>
                                        <Button
                                            type="button"
                                            @click="publish"
                                            :disabled="form.processing"
                                            class="bg-amber-600 hover:bg-amber-700"
                                        >
                                            <svg
                                                v-if="form.processing"
                                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                ></circle>
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                            Publish
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* TipTap Editor Styles */
.ProseMirror {
    outline: none;
}

.ProseMirror p.is-editor-empty:first-child::before {
    content: attr(data-placeholder);
    float: left;
    color: #adb5bd;
    pointer-events: none;
    height: 0;
}

.ProseMirror h1 {
    font-size: 2em;
    font-weight: bold;
    margin-top: 0.67em;
    margin-bottom: 0.67em;
}

.ProseMirror h2 {
    font-size: 1.5em;
    font-weight: bold;
    margin-top: 0.75em;
    margin-bottom: 0.75em;
}

.ProseMirror h3 {
    font-size: 1.17em;
    font-weight: bold;
    margin-top: 0.83em;
    margin-bottom: 0.83em;
}

.ProseMirror ul,
.ProseMirror ol {
    padding-left: 2rem;
    margin: 1rem 0;
}

.ProseMirror blockquote {
    border-left: 3px solid #d1d5db;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
}

.ProseMirror code {
    background-color: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-family: monospace;
}

.ProseMirror pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
}

.ProseMirror img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
}

.ProseMirror a {
    color: #f59e0b;
    text-decoration: underline;
}

.ProseMirror hr {
    border: none;
    border-top: 2px solid #e5e7eb;
    margin: 2rem 0;
}
</style>

