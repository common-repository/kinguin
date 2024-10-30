class ImportProducts {


	constructor() {
		this.setupContainer               = document.getElementById( "kinguin-import-setup" );
		this.importButton                 = this.setupContainer.querySelector( '.start-import' );
		this.importButton.onclick         = () => this.createCacheDir();
		this.ajaxUrl                      = kinguin.ajax_url;
		this.totalPages                   = parseInt( kinguin.total );
		this.progress                     = 0;
        this.progressCacheProductsUpdate  = false;
        this.progressImportProductsUpdate = false;
		this.cachedFiles                  = kinguin.cachedFiles;
		this.importIsRunning              = false;
	}



	/**
	 * Create cache dir.
	 */
	createCacheDir() {
		let parent = this;
		jQuery.ajax({
			type: 'POST',
			url: parent.ajaxUrl,
			dataType: 'json',
			tryCount : 0,
			retryLimit : 3,
			async: true,
			data: {
				action: 'set_cache',
                nonce:  kinguin.ajax_nonce,
			},
			beforeSend: function() {
				parent.setupContainer.querySelector( '.import-setup__begin' ).style.display   = "none";
				parent.setupContainer.querySelector( '.import-setup__process' ).style.display = "block";
				parent.importButton.disabled = true;
			},
			error : function(xhr, textStatus, errorThrown ) {
				if ( textStatus == 'timeout' ) {
					this.tryCount++;
					if ( this.tryCount <= this.retryLimit ) {
						jQuery.ajax( this );
						return;
					}
					return;
				}
			}
		}).done( function( data ){

			let status           = parent.setupContainer.querySelector( '.status_cache_dir' );
				status.innerHTML = 'Done';
				status.classList.add( 'success' );

			parent.cacheProducts( kinguin.page );
		});
	}



	/**
	 * Import products from Kinguin API to cache files.
	 */
	cacheProducts( page ) {
		let parent = this;
		jQuery.ajax({
			type: 'POST',
			url: parent.ajaxUrl,
			dataType: 'json',
			tryCount: 0,
			retryLimit: 3,
			async: true,
			data: {
				action: 'import_products_to_cache',
                nonce:  kinguin.ajax_nonce,
				page:   page
			},
			error: function (xhr, textStatus, errorThrown) {
				if (textStatus == 'timeout') {
					this.tryCount++;
					if ( this.tryCount <= this.retryLimit ) {
						jQuery.ajax( this );
						return;
					}
					return;
				}
			}
		}).done( function( response ) {
			if ( response.success === true ) {

				let statusContainer = parent.setupContainer.querySelector( '.status_cache' );

				parent.totalPages = response.data.of;            // Set total pages to import from Kinguin.
				parent.cachedFiles.push( response.data.file );   // Add file to array of existing files.

				// Update progress position once, only at the beginning.
                if ( page > 1 && parent.progressCacheProductsUpdate == false ) {
                    parent.progress = parseInt( page );
                    parent.progressCacheProductsUpdate = true;
                } else {
                    // Progress update.
                    parent.progress++;
                }
                parent.setProgressBar();
                statusContainer.innerHTML = page + ' of ' + parent.totalPages;

				// Create next file.
				if ( page < parent.totalPages ) {
					parent.cacheProducts( parseInt( page ) + 1 );
				}

				// Import products to WooCommerce.
				if ( parent.importIsRunning === false ) {
					parent.importProducts();
				}

				// Set Done after completed.
				if ( page == parent.totalPages ) {
					statusContainer.classList.add( 'success' );
					statusContainer.innerHTML = 'Done';
				}
			}
		});
	}



	/**
	 * Import products to WooCommerce
	 */
	importProducts() {
		let parent = this;
		if ( parent.cachedFiles.length > 0 ) {
			jQuery.ajax({
				type: 'POST',
				url: parent.ajaxUrl,
				dataType: 'json',
				tryCount: 0,
				retryLimit: 3,
				data: {
					action:      'import_products_to_woocommerce',
                    nonce:       kinguin.ajax_nonce,
					file:        parent.cachedFiles[0],
					total_pages: parent.totalPages,
				},
				beforeSend: function() {
					parent.importIsRunning = true;
				},
				error: function( xhr, textStatus, errorThrown ) {
					parent.importIsRunning = false;
					if (textStatus === 'timeout') {
						this.tryCount++;
						if ( this.tryCount <= this.retryLimit ) {
							jQuery.ajax( this );
							return;
						}
						return;
					}
				}
			}).done( function( response ) {
				if ( response.success === true ) {

					let statusContainer = parent.setupContainer.querySelector( '.status_import' );

                    // Update progress position once, only at the beginning.
                    if ( parseInt( response.data.page ) > 1 && parent.progressImportProductsUpdate == false ) {
                        parent.progress = parent.progress + parseInt( response.data.page );
                        parent.progressImportProductsUpdate = true;
                    } else {
                        // Progress update.
                        parent.progress++;
                    }
                    parent.setProgressBar();
					statusContainer.innerHTML = response.data.page + ' of ' + parent.totalPages;

					parent.cachedFiles.shift();
					parent.importProducts();

                    // Set Done after completed.
                    if ( response.data.page === parent.totalPages ) {
                        statusContainer.classList.add( 'success' );
                        statusContainer.innerHTML = 'Done';
                        parent.setupContainer.querySelector( '.import-setup__process' ).style.display = "none";
                        parent.setupContainer.querySelector( '.import-setup__done' ).style.display = "block";
                    }
				}
			});
		}
	}



	/**
	 * Set progressbar state
	 */
	setProgressBar() {
		let importProgress   = this.setupContainer.querySelector( '#import-progress' );
        importProgress.max   = parseInt( this.totalPages ) * 2;
        importProgress.value = parseInt( this.progress );
	}
}

new ImportProducts();