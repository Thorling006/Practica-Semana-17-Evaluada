<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Props received from Laravel
defineProps({
    categories: {
        type: Array,
        default: () => []
    }
});

// Inertia Form Setup
const form = useForm({
    nombre: '',
    autor: '',
    editorial: '',
    edicion: '',
    fecha_lanzamiento: '',
    imagen_portada: null,
    category_id: ''
});

// Image Preview State
const imagePreview = ref(null);
const fileInput = ref(null);

// Trigger file input click
const triggerFileInput = () => {
    fileInput.value.click();
};

// Handle File Change & Live Preview
const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    form.imagen_portada = file;

    // Generate Preview URL
    const reader = new FileReader();
    reader.onload = (event) => {
        imagePreview.value = event.target.result;
    };
    reader.readAsDataURL(file);
};

// Submit Form
const submitForm = () => {
    form.post('/libros', {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        }
    });
};
</script>

<template>
    <Head title="Registrar Nuevo Libro" />

    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="navbar-container">
            <Link href="/" class="nav-logo text-gradient">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open"><path d="M12 7v14"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1 4 4 0 0 0-4-4h-5a4 4 0 0 0-4 4H3z"/></svg>
                Librería La Medalla
            </Link>
            <div class="nav-links">
                <Link href="/" class="btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Volver al Catálogo
                </Link>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="main-container" style="max-width: 800px;">
        <header style="margin-bottom: 32px; text-align: center;">
            <h1 class="text-gradient" style="font-size: 2.3rem; margin-bottom: 8px;">Añadir Nuevo Libro</h1>
            <p style="color: var(--text-secondary);">Completa el formulario para registrar un libro en el inventario.</p>
        </header>

        <!-- Glassmorphism Form Card -->
        <div class="glass-panel" style="padding: 40px; border-radius: 20px;">
            <form @submit.prevent="submitForm">
                <div style="display: grid; grid-template-columns: 1fr; md:grid-template-columns: 1fr 1fr; gap: 32px;">
                    
                    <!-- Cover Image Upload Column -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <span class="form-label">Imagen de Portada</span>
                        
                        <!-- Hidden File Input -->
                        <input 
                            ref="fileInput" 
                            type="file" 
                            accept="image/*" 
                            style="display: none;" 
                            @change="handleFileChange"
                        />
                        
                        <!-- Drag/Click Preview Box -->
                        <div class="preview-box" @click="triggerFileInput">
                            <template v-if="imagePreview">
                                <img :src="imagePreview" alt="Vista previa" class="preview-img" />
                            </template>
                            <template v-else>
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary); margin-bottom: 12px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                <span style="font-weight: 600; font-size: 0.95rem; margin-bottom: 4px;">Subir Portada</span>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);">Haz clic para seleccionar</span>
                            </template>
                        </div>
                        
                        <div v-if="form.errors.imagen_portada" class="form-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ form.errors.imagen_portada }}
                        </div>
                        
                        <span style="font-size: 0.8rem; color: var(--text-secondary); text-align: center; display: block; margin-top: 4px;">
                            Soporta: JPG, PNG, WEBP, GIF (Máx. 2MB)
                        </span>
                    </div>

                    <!-- Book Information Column -->
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        
                        <!-- Title Field -->
                        <div class="form-group">
                            <label for="nombre" class="form-label">Título del Libro *</label>
                            <input 
                                id="nombre" 
                                v-model="form.nombre" 
                                type="text" 
                                placeholder="Ej. Cien Años de Soledad" 
                                class="form-control"
                                :disabled="form.processing"
                            />
                            <div v-if="form.errors.nombre" class="form-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ form.errors.nombre }}
                            </div>
                        </div>

                        <!-- Author Field -->
                        <div class="form-group">
                            <label for="autor" class="form-label">Autor *</label>
                            <input 
                                id="autor" 
                                v-model="form.autor" 
                                type="text" 
                                placeholder="Ej. Gabriel García Márquez" 
                                class="form-control"
                                :disabled="form.processing"
                            />
                            <div v-if="form.errors.autor" class="form-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ form.errors.autor }}
                            </div>
                        </div>

                        <!-- Category Selector -->
                        <div class="form-group">
                            <label for="category_id" class="form-label">Categoría del Libro *</label>
                            <select 
                                id="category_id" 
                                v-model="form.category_id" 
                                class="form-control"
                                :disabled="form.processing"
                            >
                                <option value="" disabled selected>Seleccione una categoría</option>
                                <option 
                                    v-for="cat in categories" 
                                    :key="cat.id" 
                                    :value="cat.id"
                                >
                                    {{ cat.nombre }}
                                </option>
                            </select>
                            <div v-if="form.errors.category_id" class="form-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ form.errors.category_id }}
                            </div>
                        </div>

                        <!-- Editorial & Edition in Grid -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <!-- Editorial -->
                            <div class="form-group">
                                <label for="editorial" class="form-label">Editorial *</label>
                                <input 
                                    id="editorial" 
                                    v-model="form.editorial" 
                                    type="text" 
                                    placeholder="Ej. Editorial Sudamericana" 
                                    class="form-control"
                                    :disabled="form.processing"
                                />
                                <div v-if="form.errors.editorial" class="form-error">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ form.errors.editorial }}
                                </div>
                            </div>
                            
                            <!-- Edition -->
                            <div class="form-group">
                                <label for="edicion" class="form-label">Edición *</label>
                                <input 
                                    id="edicion" 
                                    v-model="form.edicion" 
                                    type="text" 
                                    placeholder="Ej. 1ra Edición" 
                                    class="form-control"
                                    :disabled="form.processing"
                                />
                                <div v-if="form.errors.edicion" class="form-error">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    {{ form.errors.edicion }}
                                </div>
                            </div>
                        </div>

                        <!-- Release Date -->
                        <div class="form-group">
                            <label for="fecha_lanzamiento" class="form-label">Fecha de Lanzamiento *</label>
                            <input 
                                id="fecha_lanzamiento" 
                                v-model="form.fecha_lanzamiento" 
                                type="date" 
                                class="form-control"
                                :disabled="form.processing"
                            />
                            <div v-if="form.errors.fecha_lanzamiento" class="form-error">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ form.errors.fecha_lanzamiento }}
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Button Actions -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 24px; margin-top: 32px; display: flex; justify-content: flex-end; gap: 16px;">
                    <Link href="/" class="btn-secondary" :disabled="form.processing">
                        Cancelar
                    </Link>
                    <button 
                        type="submit" 
                        class="btn-primary" 
                        :disabled="form.processing"
                    >
                        <template v-if="form.processing">
                            <svg class="animate-spin" style="margin-right: 8px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                            Procesando...
                        </template>
                        <template v-else>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V3"/></svg>
                            Registrar Libro
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </main>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Adjustments for responsive inputs */
@media (min-width: 768px) {
    form > div {
        grid-template-columns: 1fr 1.3fr !important;
    }
}
</style>
