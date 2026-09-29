<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import {PhoneCall, Mail} from "@lucide/vue";
import { computed } from 'vue';

interface Category {
    id: number;
    slug: string;
    name: string;
}

const currentYear = computed(() => new Date().getFullYear());

const page = usePage();
const categories = page.props.footer_product_categories as Category[] || [];
</script>

<template>
    <footer class="text-accent-foreground pt-4" style="background: url('/assets/images/general/footer.webp') var(--accent) left top;">
        <div class="container-fluid px-4 lg:px-16 py-4 grid lg:grid-cols-4 gap-8">
            <div class="branding space-y-2">
                <p class="font-bold">Renfa Concrete LTD</p>
                <p>Plastic, ABS and steel moulds for pavers, balusters, ventilation blocks, cladding and cast-concrete garden pieces - plus the mixers, vibrators and oxide pigments to run a full production line. All held in stock in Nairobi, ready to move.</p>

                <div class="address space-y-2 mt-4">
                    <p>Maasai Road Highrise</p>
                    <p>Maasai Road, off Mombasa Road</p>
                </div>
            </div>

            <div class="quick-navigation space-y-2">
                <p class="font-bold">Quick Navigation</p>
                <div class="links grid gap-2">
                    <a href="/">Home</a>
                    <a href="/catalogue">Catalogue</a>
                    <a href="/contact">Contact Us</a>
                </div>
            </div>

            <div class="categories space-y-2">
                <p class="font-bold">Categories</p>
                <div class="links grid gap-2" v-if="categories.length > 0">
                    <a v-for="category in categories" :key="category.id" :href="`/categories/${category.slug}`">{{ category.name }}</a>
                </div>

                <div class="empty-links" v-else>
                    <p>There's  no available categories!</p>
                </div>
            </div>

            <div class="contact space-y-2">
                <p class="font-bold">Contact Us</p>
                <p class="flex items-center gap-2">
                    <PhoneCall class="w-4 h-4"/>
                    <span>+254 708 887 179</span></p>
                <p class="flex items-center gap-2">
                    <PhoneCall class="w-4 h-4"/>
                    <span>+254 115 706 106</span></p>
                <p class="flex items-center gap-2">
                    <PhoneCall class="w-4 h-4"/>
                    <span>+254 799 921 366</span></p>
                <p class="flex items-center gap-2">
                    <Mail class="w-4 h-4"/>
                    <span>info@renfaconcreteltd.com</span>
                </p>
            </div>
        </div>

        <div class="copyrights grid gap-1 lg:flex lg:justify-between border-t border-border pt-4 px-4 lg:px-16 py-4">
            <p class="text">&copy; {{  currentYear  }}. Renfa. All rights reserved.</p>

            <div class="documents space-x-2">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</template>