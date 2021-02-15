if(ppc_features.disabled_panel){
    var disabled_panel = ppc_features.disabled_panel;
    var disabled_panel = disabled_panel.split(',');
    
    if(disabled_panel.includes("taxonomy-panel-category")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'taxonomy-panel-category' ) ; // category
    }
    if(disabled_panel.includes("taxonomy-panel-post_tag")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'taxonomy-panel-post_tag' ); // tags
    }
    if(disabled_panel.includes("featured-image")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'featured-image' ); // featured image
    }
    if(disabled_panel.includes("post-link")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'post-link' ); // permalink
    }
    if(disabled_panel.includes("page-attributes")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'page-attributes' ); // page attributes
    }
    if(disabled_panel.includes("post-excerpt")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'post-excerpt' ); // Excerpt
    }
    if(disabled_panel.includes("discussion-panel")){
        wp.data.dispatch('core/edit-post').removeEditorPanel( 'discussion-panel' ); // Discussion
    }
    if(disabled_panel.includes("post-status")){
        wp.data.dispatch( 'core/edit-post').removeEditorPanel( 'post-status' ) ;// Post status
    }
}