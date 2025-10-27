<?php
/**
 * Models Manager View
 *
 * @package ShiftZoneRAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap szr-admin-wrap">
    <h1 class="szr-admin-title">
        <span class="dashicons dashicons-car"></span>
        <?php _e( 'Gestion des Modèles', 'shiftzoner-admin' ); ?>
    </h1>

    <div class="szr-toolbar">
        <button class="button" id="szr-bulk-delete-models" disabled>
            <span class="dashicons dashicons-trash"></span>
            <?php _e( 'Supprimer la sélection', 'shiftzoner-admin' ); ?>
        </button>
        <button class="button" id="szr-bulk-move-models" disabled>
            <span class="dashicons dashicons-move"></span>
            <?php _e( 'Déplacer la sélection', 'shiftzoner-admin' ); ?>
        </button>
        <button class="button" id="szr-import-models">
            <span class="dashicons dashicons-upload"></span>
            <?php _e( 'Importer des modèles (CSV)', 'shiftzoner-admin' ); ?>
        </button>
    </div>

    <div class="szr-models-table-container">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="check-column"><input type="checkbox" id="szr-select-all-models"></th>
                    <th><?php _e( 'Modèle', 'shiftzoner-admin' ); ?></th>
                    <th><?php _e( 'Marque', 'shiftzoner-admin' ); ?></th>
                    <th><?php _e( 'Photos', 'shiftzoner-admin' ); ?></th>
                    <th><?php _e( 'Actions', 'shiftzoner-admin' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $models as $parent ) : ?>
                    <?php
                    $children = get_terms( array(
                        'taxonomy'   => SZR_TAX_MODEL,
                        'hide_empty' => false,
                        'parent'     => $parent->term_id,
                    ) );
                    ?>
                    <?php if ( ! empty( $children ) ) : ?>
                        <tr class="szr-parent-row">
                            <td colspan="5"><strong><?php echo esc_html( $parent->name ); ?></strong></td>
                        </tr>
                        <?php foreach ( $children as $model ) : ?>
                            <tr>
                                <td class="check-column">
                                    <input type="checkbox" class="szr-model-checkbox" value="<?php echo esc_attr( $model->term_id ); ?>">
                                </td>
                                <td><?php echo esc_html( $model->name ); ?></td>
                                <td><?php echo esc_html( $parent->name ); ?></td>
                                <td><?php echo esc_html( $model->count ); ?></td>
                                <td>
                                    <button class="button button-small szr-edit-model-btn" data-model-id="<?php echo esc_attr( $model->term_id ); ?>">
                                        <?php _e( 'Modifier', 'shiftzoner-admin' ); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
