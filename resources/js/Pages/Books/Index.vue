<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

// Props received from Laravel controller
const props = defineProps({
    books: {
        type: Array,
        default: () => []
    },
    categories: {
        type: Array,
        default: () => []
    },
    flash: {
        type: Object,
        default: () => ({ success: null })
    }
});

// State
const searchQuery = ref('');
const selectedCategory = ref('all');
const showToast = ref(false);
const toastMessage = ref('');

// Setup Toast Notification
onMounted(() => {
    if (props.flash?.success) {
        toastMessage.value = props.flash.success;
        showToast.value = true;
        setTimeout(() => {
            showToast.value = false;
        }, 5000);
    }
});

// Category Badge Helper
const getCategoryBadgeClass = (categoryName) => {
    if (!categoryName) return 'badge-default';
    
    const name = categoryName.toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "") // remove accents
        .replace(/\s+/g, '-');           // replace spaces with hyphens
        
    const knownBadges = [
        'ficcion', 
        'fantasia', 
        'ciencia-ficcion', 
        'tecnologia-y-programacion', 
        'historia', 
        'desarrollo-personal'
    ];
    
    return knownBadges.includes(name) ? `badge-${name}` : 'badge-default';
};

// Format Date
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'UTC'
    });
};

// Filtered and Searched Books
const filteredBooks = computed(() => {
    return props.books.filter(book => {
        const matchesSearch = 
            book.nombre.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            book.autor.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            book.editorial.toLowerCase().includes(searchQuery.value.toLowerCase());
            
        const matchesCategory = 
            selectedCategory.value === 'all' || 
            book.category_id === parseInt(selectedCategory.value);
            
        return matchesSearch && matchesCategory;
    });
});
</script>

<template>
    <Head title="Inventario de Libros" />

    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="navbar-container">
            <Link href="/" class="nav-logo text-gradient">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open"><path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1 4 4 0 0 0-4-4h-5a4 4 0 0 0-4 4H3z"/></svg>
                Librería La Medalla
            </Link>
            <div class="nav-links">
                <Link href="/libros/crear" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Añadir Libro
                </Link>
            </div>
        </div>
    </nav>

    <!-- Success Toast Notification -->
    <div v-if="showToast" class="toast-msg">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ toastMessage }}
    </div>

    <!-- Main Container -->
    <main class="main-container">
        <!-- Title & Search Bar -->
        <header class="catalog-header">
            <div>
                <h1 class="text-gradient" style="font-size: 2.5rem; margin-bottom: 8px;">Inventario de Libros</h1>
                <p style="color: var(--text-secondary);">Explora, busca y gestiona las existencias en nuestra biblioteca virtual.</p>
            </div>
            
            <div style="position: relative; width: 100%; max-width: 380px;">
                <input 
                    v-model="searchQuery" 
                    type="text" 
                    placeholder="Buscar por título, autor o editorial..." 
                    class="form-control"
                    style="padding-left: 44px;"
                />
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    width="18" 
                    height="18" 
                    viewBox="0 0 24 24" 
                    fill="none" 
                    stroke="currentColor" 
                    stroke-width="2" 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-secondary);"
                >
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
        </header>

        <!-- Category Filters -->
        <div class="filter-bar">
            <button 
                @click="selectedCategory = 'all'" 
                :class="['filter-btn', { active: selectedCategory === 'all' }]"
            >
                Todas las categorías
            </button>
            <button 
                v-for="cat in categories" 
                :key="cat.id" 
                @click="selectedCategory = cat.id"
                :class="['filter-btn', { active: selectedCategory === cat.id }]"
            >
                {{ cat.nombre }}
            </button>
        </div>

        <!-- Books Grid -->
        <div v-if="filteredBooks.length > 0" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 32px;">
            <article 
                v-for="book in filteredBooks" 
                :key="book.id" 
                class="glass-panel book-card"
                style="padding: 16px;"
            >
                <div class="book-cover-container">
                    <img 
                        :src="book.imagen_portada" 
                        :alt="book.nombre" 
                        class="book-cover"
                        loading="lazy"
                    />
                    <div class="book-category-floating">
                        <span :class="['badge', getCategoryBadgeClass(book.category?.nombre)]">
                            {{ book.category?.nombre || 'Sin Categoría' }}
                        </span>
                    </div>
                </div>

                <div class="book-details">
                    <h3 class="book-title">{{ book.nombre }}</h3>
                    <p class="book-author">por {{ book.autor }}</p>
                    
                    <div class="book-meta-grid">
                        <div class="meta-item">
                            <span class="meta-label">Editorial</span>
                            <span class="meta-val" :title="book.editorial">{{ book.editorial }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Edición</span>
                            <span class="meta-val" :title="book.edicion">{{ book.edicion }}</span>
                        </div>
                        <div class="meta-item" style="grid-column: span 2;">
                            <span class="meta-label">Fecha Lanzamiento</span>
                            <span class="meta-val">{{ formatDate(book.fecha_lanzamiento) }}</span>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <!-- Empty State -->
        <div v-else class="glass-panel empty-state">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-library"><path d="m16 6 4 14"/><path d="M12 6v14"/><path d="M8 8v12"/><path d="M4 4v16"/></svg>
            </div>
            <h2 class="text-gradient-purple" style="font-size: 1.8rem; font-weight: 700;">No se encontraron libros</h2>
            <p style="color: var(--text-secondary); max-width: 420px; margin: 0 auto 12px;">
                No hay ningún libro registrado que coincida con los criterios de búsqueda o filtro aplicados en este momento.
            </p>
            <Link v-if="books.length === 0" href="/libros/crear" class="btn-primary" style="margin-top: 12px;">
                Registrar primer libro
            </Link>
        </div>
    </main>
</template>

<style scoped>
/* Scoped adjustments for animations if needed */
</style>
