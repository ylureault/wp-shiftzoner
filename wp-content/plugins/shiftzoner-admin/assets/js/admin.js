/**
 * ShiftZoneR Admin JavaScript
 */

(function($) {
    'use strict';

    let currentBrandId = null;
    let mediaUploader = null;

    // Initialize on document ready
    $(document).ready(function() {
        initBrandsManager();
        initModals();
        initMediaUploader();
    });

    /**
     * Initialize Brands Manager
     */
    function initBrandsManager() {
        // Click on brand item
        $(document).on('click', '.szr-brand-item', function(e) {
            if ($(e.target).closest('.szr-brand-actions').length) {
                return;
            }

            $('.szr-brand-item').removeClass('active');
            $(this).addClass('active');

            currentBrandId = $(this).data('brand-id');
            loadModels(currentBrandId);

            // Enable add model button
            $('#szr-add-model').prop('disabled', false);
        });

        // Add brand button
        $('#szr-add-brand').on('click', function() {
            openBrandModal();
        });

        // Add model button
        $('#szr-add-model').on('click', function() {
            if (currentBrandId) {
                openModelModal(currentBrandId);
            }
        });

        // Edit brand button
        $(document).on('click', '.szr-edit-brand', function(e) {
            e.stopPropagation();
            const brandId = $(this).data('brand-id');
            const $brandItem = $(this).closest('.szr-brand-item');
            const brandName = $brandItem.find('.szr-brand-name').text();
            const logoSrc = $brandItem.find('.szr-brand-logo').attr('src');

            openBrandModal(brandId, brandName, logoSrc);
        });

        // Delete brand button
        $(document).on('click', '.szr-delete-brand', function(e) {
            e.stopPropagation();
            const brandId = $(this).data('brand-id');
            if (confirm('Êtes-vous sûr de vouloir supprimer cette marque et tous ses modèles ?')) {
                deleteBrand(brandId);
            }
        });

        // Edit model button
        $(document).on('click', '.szr-edit-model', function(e) {
            e.stopPropagation();
            const modelId = $(this).data('model-id');
            const $modelItem = $(this).closest('.szr-model-item');
            const modelName = $modelItem.find('.szr-model-name').text();

            openModelModal(currentBrandId, modelId, modelName);
        });

        // Delete model button
        $(document).on('click', '.szr-delete-model', function(e) {
            e.stopPropagation();
            const modelId = $(this).data('model-id');
            if (confirm('Êtes-vous sûr de vouloir supprimer ce modèle ?')) {
                deleteModel(modelId);
            }
        });

        // Search brands
        let searchTimeout;
        $('#szr-search-brands').on('input', function() {
            clearTimeout(searchTimeout);
            const query = $(this).val();
            searchTimeout = setTimeout(function() {
                searchBrands(query);
            }, 300);
        });

        // Brand form submit
        $('#szr-brand-form').on('submit', function(e) {
            e.preventDefault();
            saveBrand();
        });

        // Model form submit
        $('#szr-model-form').on('submit', function(e) {
            e.preventDefault();
            saveModel();
        });
    }

    /**
     * Initialize Modals
     */
    function initModals() {
        // Close modal on overlay click
        $(document).on('click', '.szr-modal-overlay', function() {
            $(this).closest('.szr-modal').hide();
        });

        // Close modal on close button
        $(document).on('click', '.szr-modal-close, .szr-modal-cancel', function() {
            $(this).closest('.szr-modal').hide();
        });
    }

    /**
     * Initialize Media Uploader
     */
    function initMediaUploader() {
        // Upload logo button
        $(document).on('click', '#szr-upload-logo', function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Choisir un logo',
                button: {
                    text: 'Utiliser ce logo'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#szr-brand-logo-id').val(attachment.id);
                $('#szr-logo-preview').html('<img src="' + attachment.url + '" alt="">');
                $('#szr-remove-logo').show();
            });

            mediaUploader.open();
        });

        // Remove logo button
        $(document).on('click', '#szr-remove-logo', function(e) {
            e.preventDefault();
            $('#szr-brand-logo-id').val('');
            $('#szr-logo-preview').html('<span class="dashicons dashicons-format-image"></span>');
            $(this).hide();
        });
    }

    /**
     * Open Brand Modal
     */
    function openBrandModal(brandId = null, brandName = '', logoSrc = '') {
        const $modal = $('#szr-brand-modal');
        const $form = $('#szr-brand-form');

        $form[0].reset();

        if (brandId) {
            $('#szr-brand-modal-title').text('Modifier la Marque');
            $('#szr-brand-id').val(brandId);
            $('#szr-brand-name').val(brandName);

            if (logoSrc) {
                $('#szr-logo-preview').html('<img src="' + logoSrc + '" alt="">');
                $('#szr-remove-logo').show();
            } else {
                $('#szr-logo-preview').html('<span class="dashicons dashicons-format-image"></span>');
                $('#szr-remove-logo').hide();
            }
        } else {
            $('#szr-brand-modal-title').text('Ajouter une Marque');
            $('#szr-brand-id').val('');
            $('#szr-logo-preview').html('<span class="dashicons dashicons-format-image"></span>');
            $('#szr-remove-logo').hide();
        }

        $modal.show();
    }

    /**
     * Open Model Modal
     */
    function openModelModal(brandId, modelId = null, modelName = '') {
        const $modal = $('#szr-model-modal');
        const $form = $('#szr-model-form');

        $form[0].reset();
        $('#szr-model-brand-id').val(brandId);

        if (modelId) {
            $('#szr-model-modal-title').text('Modifier le Modèle');
            $('#szr-model-id').val(modelId);
            $('#szr-model-name').val(modelName);
        } else {
            $('#szr-model-modal-title').text('Ajouter un Modèle');
            $('#szr-model-id').val('');
        }

        $modal.show();
    }

    /**
     * Load Models for Brand
     */
    function loadModels(brandId) {
        const $container = $('#szr-models-items');
        const $placeholder = $('#szr-models-placeholder');

        $container.html('<p style="text-align:center;"><span class="szr-loading"></span></p>').show();
        $placeholder.hide();

        $.ajax({
            url: szrAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'szr_admin_get_models',
                nonce: szrAdmin.nonce,
                brand_id: brandId
            },
            success: function(response) {
                if (response.success) {
                    displayModels(response.data.models);
                } else {
                    showError(response.data.message || 'Erreur lors du chargement des modèles');
                    $container.hide();
                    $placeholder.show();
                }
            },
            error: function() {
                showError('Erreur lors du chargement des modèles');
                $container.hide();
                $placeholder.show();
            }
        });
    }

    /**
     * Display Models
     */
    function displayModels(models) {
        const $container = $('#szr-models-items');

        if (!models || models.length === 0) {
            $container.html('<p class="szr-no-data">Aucun modèle pour cette marque</p>');
            return;
        }

        let html = '';
        models.forEach(function(model) {
            html += `
                <div class="szr-model-item" data-model-id="${model.id}">
                    <div class="szr-model-details">
                        <div class="szr-model-name">${escapeHtml(model.name)}</div>
                        <div class="szr-model-count">${model.count} photos</div>
                    </div>
                    <div class="szr-model-actions">
                        <button class="button button-small szr-edit-model" data-model-id="${model.id}" title="Modifier">
                            <span class="dashicons dashicons-edit"></span>
                        </button>
                        <button class="button button-small szr-delete-model" data-model-id="${model.id}" title="Supprimer">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
            `;
        });

        $container.html(html);
    }

    /**
     * Save Brand
     */
    function saveBrand() {
        const $form = $('#szr-brand-form');
        const $button = $form.find('button[type="submit"]');
        const brandId = $('#szr-brand-id').val();

        const data = {
            action: brandId ? 'szr_admin_edit_brand' : 'szr_admin_add_brand',
            nonce: szrAdmin.nonce,
            brand_id: brandId,
            name: $('#szr-brand-name').val(),
            slug: $('#szr-brand-slug').val(),
            logo_id: $('#szr-brand-logo-id').val()
        };

        $button.prop('disabled', true).text('Enregistrement...');

        $.ajax({
            url: szrAdmin.ajaxUrl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    showSuccess(response.data.message);
                    $('#szr-brand-modal').hide();
                    location.reload(); // Reload to update the list
                } else {
                    showError(response.data.message || 'Erreur lors de l\'enregistrement');
                    $button.prop('disabled', false).text('Enregistrer');
                }
            },
            error: function() {
                showError('Erreur lors de l\'enregistrement');
                $button.prop('disabled', false).text('Enregistrer');
            }
        });
    }

    /**
     * Save Model
     */
    function saveModel() {
        const $form = $('#szr-model-form');
        const $button = $form.find('button[type="submit"]');
        const modelId = $('#szr-model-id').val();

        const data = {
            action: modelId ? 'szr_admin_edit_model' : 'szr_admin_add_model',
            nonce: szrAdmin.nonce,
            model_id: modelId,
            brand_id: $('#szr-model-brand-id').val(),
            name: $('#szr-model-name').val(),
            slug: $('#szr-model-slug').val()
        };

        $button.prop('disabled', true).text('Enregistrement...');

        $.ajax({
            url: szrAdmin.ajaxUrl,
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    showSuccess(response.data.message);
                    $('#szr-model-modal').hide();
                    loadModels(currentBrandId); // Reload models list
                } else {
                    showError(response.data.message || 'Erreur lors de l\'enregistrement');
                    $button.prop('disabled', false).text('Enregistrer');
                }
            },
            error: function() {
                showError('Erreur lors de l\'enregistrement');
                $button.prop('disabled', false).text('Enregistrer');
            }
        });
    }

    /**
     * Delete Brand
     */
    function deleteBrand(brandId) {
        $.ajax({
            url: szrAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'szr_admin_delete_brand',
                nonce: szrAdmin.nonce,
                brand_id: brandId
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.data.message);
                    $('.szr-brand-item[data-brand-id="' + brandId + '"]').fadeOut(function() {
                        $(this).remove();
                    });

                    // Clear models if this brand was selected
                    if (currentBrandId == brandId) {
                        currentBrandId = null;
                        $('#szr-models-items').hide();
                        $('#szr-models-placeholder').show();
                        $('#szr-add-model').prop('disabled', true);
                    }
                } else {
                    showError(response.data.message || 'Erreur lors de la suppression');
                }
            },
            error: function() {
                showError('Erreur lors de la suppression');
            }
        });
    }

    /**
     * Delete Model
     */
    function deleteModel(modelId) {
        $.ajax({
            url: szrAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'szr_admin_delete_model',
                nonce: szrAdmin.nonce,
                model_id: modelId
            },
            success: function(response) {
                if (response.success) {
                    showSuccess(response.data.message);
                    $('.szr-model-item[data-model-id="' + modelId + '"]').fadeOut(function() {
                        $(this).remove();
                    });
                } else {
                    showError(response.data.message || 'Erreur lors de la suppression');
                }
            },
            error: function() {
                showError('Erreur lors de la suppression');
            }
        });
    }

    /**
     * Search Brands
     */
    function searchBrands(query) {
        const $items = $('.szr-brand-item');

        if (!query) {
            $items.show();
            return;
        }

        const lowerQuery = query.toLowerCase();
        $items.each(function() {
            const brandName = $(this).find('.szr-brand-name').text().toLowerCase();
            if (brandName.includes(lowerQuery)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    /**
     * Show Success Message
     */
    function showSuccess(message) {
        // Use WordPress native notices
        const $notice = $('<div class="notice notice-success is-dismissible"><p>' + message + '</p></div>');
        $('.szr-admin-wrap').prepend($notice);
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    /**
     * Show Error Message
     */
    function showError(message) {
        // Use WordPress native notices
        const $notice = $('<div class="notice notice-error is-dismissible"><p>' + message + '</p></div>');
        $('.szr-admin-wrap').prepend($notice);
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

})(jQuery);
