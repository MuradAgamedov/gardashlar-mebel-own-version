/**
 * Multi-language Slug Generator
 * Supports Azerbaijani, Russian, and English languages
 */

class SlugGenerator {
    constructor() {
        // Character mappings for different languages
        this.mappings = {
            // Azerbaijani characters
            'ə': 'e', 'ı': 'i', 'ö': 'o', 'ğ': 'g', 'ü': 'u', 'ş': 'sh', 'ç': 'ch',
            'Ə': 'E', 'I': 'I', 'Ö': 'O', 'Ğ': 'G', 'Ü': 'U', 'Ş': 'Sh', 'Ç': 'Ch',
            
            // Russian characters
            'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
            'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
            'п': 'p', 'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'ts',
            'ч': 'ch', 'ш': 'sh', 'щ': 'sch', 'ъ': '', 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
            
            'А': 'a', 'Б': 'b', 'В': 'v', 'Г': 'g', 'Д': 'd', 'Е': 'e', 'Ё': 'yo', 'Ж': 'zh',
            'З': 'z', 'И': 'i', 'Й': 'y', 'К': 'k', 'Л': 'l', 'М': 'm', 'Н': 'n', 'О': 'o',
            'П': 'p', 'Р': 'r', 'С': 's', 'Т': 't', 'У': 'u', 'Ф': 'f', 'Х': 'h', 'Ц': 'ts',
            'Ч': 'ch', 'Ш': 'sh', 'Щ': 'sch', 'Ъ': '', 'Ы': 'y', 'Ь': '', 'Э': 'e', 'Ю': 'yu', 'Я': 'ya'
        };
    }

    /**
     * Generate slug from text
     * @param {string} text - Input text
     * @returns {string} - Generated slug
     */
    generate(text) {
        if (!text) return '';

        // Convert to lowercase and replace special characters
        let slug = text.toLowerCase();

        // Replace language-specific characters
        for (const [char, replacement] of Object.entries(this.mappings)) {
            slug = slug.replace(new RegExp(char, 'g'), replacement);
        }

        // Remove any character that is not alphanumeric, space, or hyphen
        slug = slug.replace(/[^a-z0-9\s-]/g, '');

        // Replace multiple spaces with single hyphen
        slug = slug.replace(/\s+/g, '-');

        // Replace multiple hyphens with single hyphen
        slug = slug.replace(/-+/g, '-');

        // Remove hyphens from start and end
        slug = slug.replace(/^-+|-+$/g, '');

        return slug;
    }

    /**
     * Initialize slug generation for forms
     */
    initFormSlugGeneration() {
        const titleInputs = document.querySelectorAll('input[name*="[title]"]');
        
        titleInputs.forEach(titleInput => {
            titleInput.addEventListener('input', function() {
                // Extract language ID from input name
                const nameMatch = this.name.match(/translations\[(\d+)\]\[title\]/);
                if (nameMatch) {
                    const languageId = nameMatch[1];
                    const slugInput = document.querySelector(`input[name="translations[${languageId}][slug]"]`);
                    
                    if (slugInput) {
                        const slug = window.slugGenerator.generate(this.value);
                        slugInput.value = slug;
                    }
                }
            });
        });
    }
}

// Initialize slug generator
window.slugGenerator = new SlugGenerator();

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.slugGenerator.initFormSlugGeneration();
});
