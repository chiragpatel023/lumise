<?php
/**
*
*	(p) package: Lumise
*	(c) author:	King-Theme
*	(i) website: https://lumise.com
*
*/

class lumise_tmpl_register {
	
	public function reg_editor_menus() {

		global $lumise;
		
		return array(
			
			'product' => array(
				"label" => $lumise->lang('Product'),
				"icon" => "lumisex-cube",
				"callback" => "",
				"load" => "",
				"content" =>
					 '<!-- <header>
						<name></name>
						<price></price>
						<sku></sku>
						<button class="lumise-btn white" id="lumise-change-product">
							'.$lumise->lang('Change product').'
							<i class="lumisex-arrow-swap"></i>
						</button>
						<desc>
							<span></span>
							&nbsp;&nbsp;<a href="#more">'.$lumise->lang('More').'</a>
						</desc>
					</header> -->
					<div id="lumise-cart-wrp" data-view="attributes" class="smooth">
						<div class="lumise-cart-options">
							<div class="lumise-prints"></div>
								<div class="lumise-cart-attributes" id="lumise-cart-attributes"></div>
						</div>
					</div>'
			),
			
			'templates' => array(
				"label" => $lumise->lang('Templates'),
				"icon" => "lumise-icon-star",
				"callback" => "",
				"load" => "templates",
				"class" => "lumise-x-thumbn",
				"content" =>
					'<header>
						<span class="lumise-templates-search">
							<input type="search" id="lumise-templates-search-inp" placeholder="'.$lumise->lang('Search templates').'" />
							<i class="lumisex-android-search"></i>
						</span>
						<div class="lumise-template-categories" data-prevent-click="true">
							<button data-func="show-categories" data-type="templates">
								<span>'.$lumise->lang('All categories').'</span>
								<i class="lumisex-ios-arrow-forward"></i>
							</button>
						</div>
					</header>
					<div id="lumise-templates-list" class="smooth">
						<ul class="lumise-list-items">
							<i class="lumise-spinner white x3 mt2"></i>
						</ul>
					</div>'
			),
			
			'cliparts' => array(
				"label" => $lumise->lang('Cliparts'),
				"icon" => "lumise-icon-heart",
				"callback" => "",
				"load" => "cliparts",
				"class" => "lumise-x-thumbn",
				"content" =>
					'<header>
						<span class="lumise-cliparts-search">
							<input type="search" id="lumise-cliparts-search-inp" placeholder="'.$lumise->lang('Search cliparts').'" />
							<i class="lumisex-android-search"></i>
						</span>
						<div class="lumise-clipart-categories" data-prevent-click="true">
							<button data-func="show-categories" data-type="cliparts">
								<span>'.$lumise->lang('All categories').'</span>
								<i class="lumisex-ios-arrow-forward"></i>
							</button>
						</div>
					</header>
					<div id="lumise-cliparts-list" class="smooth">
						<ul class="lumise-list-items">
							<i class="lumise-spinner white x3 mt2"></i>
						</ul>
					</div>'
			),
			
			'text' => array(
				"label" => $lumise->lang('Text'),
				"icon" => "lumisex-character",
				"callback" => "",
				"load" => "",
				"class" => "smooth",
				"content" =>
					'<p class="gray">'.$lumise->lang('Click or drag to add text').'</p>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "CurvedText", "fontSize": 30, "font":["","regular"],"bridge":{"bottom":2,"curve":-4.5,"oblique":false,"offsetY":0.5,"trident":false},"type":"curvedText"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-curved.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "10", "fontSize": 100, "font":["","regular"],"type":"i-text", "charSpacing": 40, "top": -50},{"fontFamily":"Poppins","text": "Messi", "fontSize": 30, "font":["","regular"],"type":"i-text", "charSpacing": 40, "top": 10}]\' style="text-align: center;">
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-number.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Oblique","fontSize":60,"font":["","regular"],"bridge":{"bottom":4.5,"curve":10,"oblique":true,"offsetY":0.5,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-oblique.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2,"curve":-4.5,"oblique":false,"offsetY":0.5,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-1.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2,"curve":-2.5,"oblique":false,"offsetY":0.1,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-2.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2,"curve":-3,"oblique":false,"offsetY":0.5,"trident":true},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-3.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":5,"curve":5,"oblique":false,"offsetY":0.5,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-4.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2.5,"curve":2.5,"oblique":false,"offsetY":0.05,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-5.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":3,"curve":2.5,"oblique":false,"offsetY":0.5,"trident":true},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-6.png" />
					</span>
					<span id="lumise-text-mask-guide">
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-mask.png" />
					</span>
					<div id="lumise-text-ext"></div>'.
					($lumise->connector->is_admin() || $lumise->cfg->settings['user_font'] !== '0' ? '<button class="lumise-btn mb2 lumise-more-fonts">'.$lumise->lang('Load more 878+ fonts').'</button>' : '')
			),
			
			'uploads' => array(
				"label" => $lumise->lang('Images'),
				"icon" => "lumise-icon-picture",
				"callback" => "",
				"load" => "images",
				"class" => "lumise-x-thumbn",
				"content" =>
					(($lumise->connector->is_admin() || $lumise->cfg->settings['disable_resources'] != 1) ? 
					'<header class="images-from-socials lumise_form_group">
						<button class="active" data-nav="internal">
							<i class="lumise-icon-cloud-upload"></i>
							'.$lumise->lang('Upload').'
						</button>
						<button data-nav="external">
							<i class="lumise-icon-magnifier"></i>
							'.$lumise->lang('Resources').'
						</button>
					</header>' : '').
					'<div data-tab="internal" class="active">
						<div id="lumise-upload-form">
							<i class="lumise-icon-cloud-upload"></i>
							<span>'.$lumise->lang('Click or drop images here').'</span>
							<input type="file" multiple="true" />
						</div>
						<div id="lumise-upload-list">
							<ul class="lumise-list-items"></ul>
						</div>
					</div>
					<div data-tab="external" id="lumise-external-images"></div>'
			),
			
			'shapes' => array(
				"label" => $lumise->lang('Shapes'),
				"icon" => "lumisex-diamond",
				"callback" => "",
				"load" => "shapes",
				"class" => "smooth",
				"content" => ""
			),
			
			'layers' => array(
				"label" => $lumise->lang('Layers'),
				"icon" => "lumise-icon-layers",
				"callback" => "layers",
				"load" => "",
				"class" => "smooth",
				"content" => "<ul></ul>"
			),
			
			'drawing' => array(
				"label" => $lumise->lang('Drawing'),
				"icon" => "lumise-icon-note",
				"callback" => "",
				"load" => "",
				"class" => "lumise-left-form",
				"content" => 
					'<h3>'.$lumise->lang('Free drawing mode').'</h3>
					<div>
						<label>'.$lumise->lang('Size').'</label>
						<inp data-range="helper" data-value="1">
							<input id="lumise-drawing-width" data-callback="drawing" value="1" min="1" max="100" data-value="1" type="range" />
						</inp>
					</div>
					<div'.($lumise->cfg->settings['enable_colors'] == '0' ? ' class="hidden"' : '').'>
						<input id="lumise-drawing-color" placeholder="'.$lumise->lang('Click to choose color').'" type="search" class="color" />
						<span class="lumise-save-color" data-tip="true" data-target="drawing-color">
							<i class="lumisex-android-add"></i>
							<span>'.$lumise->lang('Save this color').'</span>
						</span>
					</div>
					<div>
						<ul class="lumise-color-presets" data-target="drawing-color"></ul>
					</div>
					<div class="gray">
						<span>
							<i class="lumisex-android-bulb"></i>
							'.$lumise->lang('Tips: Mouse wheel on the canvas to quick change the brush size').'
						</span>
					</div>'
			)
		);
	}
	
	public function reg_product_attributes() {
		
		global $lumise;
		
		return array(
			
			'printing' => array(
				'hidden' => true,
				'render' => ''
			),
			
			'product_color' => array(
				'label' => $lumise->lang('Product colors'),
				'unique' => true,
				'use_variation' => true,
				'values' => <<<EOF
				
					var colors = values.split(decodeURI('%0A')),
						content = '<div class="lumise-field-color-wrp rbd">\
								<ul class="lumise-field-color">';
					if (values !== '') {
						colors.map(function(c) {
					
							var c = c.split('|'),
								lb = (c[1] !== undefined ? c[1].trim() : c[0].trim());
							
							lb = lb.replace(/\"/g, '&quot;');
							
							content += '<li data-color="'+c[0].trim()+'" data-label="'+lb+'" style="background:'+c[0].trim()+'"><i class="fa fa-times" data-func="delete"></i></li>';
						
						});
					}
					
					content += '</ul>';
					
					content += '<p style="padding-top: 0px;">\
									<button class="lumise-button lumise-button-primary" data-func="create-color">\
										<i class="fa fa-plus"></i> {$lumise->lang('Add new color')}\
									</button>\
									<button class="lumise-button" data-func="clear-color">\
										<i class="fa fa-eraser"></i> {$lumise->lang('Clear all')}\
									</button>\
									<textarea data-name="values" class="hidden">'+(values !== undefined ? values : '')+'</textarea>\
								</p>\
								<p><em>{$lumise->lang('This will change the color of the product, apply to products with mask image (PNG)')}</em></p>\
							</div>';
							
					wrp.html(content);
					
					if (typeof wrp.sortable == 'function') {
						wrp.find('ul.lumise-field-color').sortable({update: function() {
							var vals = [];
							$(this).find('li[data-color]').each(function() {
								vals.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));	
							});
							$(this).closest('.lumise-field-color-wrp').find('textarea[data-name="values"]').val(
								vals.join(decodeURI('%0A'))
							).trigger('change');
						}});
					};
					
					triggerObjects.general_events.return_colors = function(wrp) {
		
						var val = [];
						
						wrp.find('li[data-color]').each(function(){
							val.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));
						});
						
						val = val.join(decodeURI('%0A'));
						
						wrp.find('textarea[data-name="values"]').val(val).trigger('change');
							
					};
					
					trigger({
						el: wrp,
						events: {
							'button[data-func="create-color"]': 'add_color',
							'button[data-func="clear-color"]': 'clear_color',
							'ul.lumise-field-color': 'color_func'
						},
						add_color: function(e) {
							e.data = triggerObjects.general_events;
							triggerObjects.general_events.create_color(e);
						},
						clear_color: function(e) {
							$(this).closest('.att-layout-body-field').find('ul.lumise-field-color').html('');
							triggerObjects.general_events.return_colors(
								$(this).closest('.att-layout-body-field')
							);
							e.preventDefault();	
						},
						color_func: function(e) {
							if (
								e.target.getAttribute('data-func') == 'delete' ||
								e.target.getAttribute('data-color') == 'delete'
							) {
								$(e.target).parent().remove();
								triggerObjects.general_events.return_colors(
									$(this).closest('.att-layout-body-field')
								);
								e.preventDefault();	
							}	
						}
					});
					
EOF
				,'render' => <<<EOF
				
					var el = $('<ul class="lumise-product-color"></ul>'), 
						valid_value = false;
						
					el.append('<li data-color="" title="{$lumise->lang('Clear color')}"></li>');
					
					data.values.map(function(v) {
						if (v.value !== '') {
							el.append('<li data-color="'+v.value+'" title="'+v.title.replace(/\"/g, '&quot;')+'" style="background-color:'+v.value+'"></li>');
							if (data.value === v.value)
								valid_value = true;
						}
					});
					
					el.append('<input type="hidden" name="'+data.id+'" class="lumise-cart-param" value="'+(valid_value ? data.value : '')+'" '+(data.required ? 'required' : '')+' />');
					
					el.find('li[data-color]').on('click', function(e) {
						$(this).parent().find('li.choosed').removeClass('choosed');
						$(this).addClass('choosed')
							   .closest('.lumise_form_content')
							   .find('input.lumise-cart-param')
							   .val(this.getAttribute('data-color'))
							   .trigger('change');
						setTimeout(lumise.func.product_color, 1, this.getAttribute('data-color'));
						e.preventDefault();
					});
					
					if (valid_value && data.value !== undefined && data.value !== '')
						el.find('li[data-color="'+data.value+'"]').trigger('click');
					
					return el;
						
EOF
			),
			
			'color' => array(
				'label' => $lumise->lang('Color picker'),
				'use_variation' => true,
				'values' => <<<EOF
				
					var colors = values.split(decodeURI('%0A')),
						content = '<div class="lumise-field-color-wrp rbd">\
								<ul class="lumise-field-color">';
					if (values !== '') {
						colors.map(function(c) {
					
							var c = c.split('|'),
								lb = (c[1] !== undefined ? c[1].trim() : c[0].trim());
							
							lb = lb.replace(/\"/g, '&quot;');
							
							content += '<li data-color="'+c[0].trim()+'" data-label="'+lb+'" style="background:'+c[0].trim()+'"><i class="fa fa-times" data-func="delete"></i></li>';
						
						});
					}
					
					content += '</ul>';
					
					content += '<p style="padding-top: 0px;">\
									<button class="lumise-button lumise-button-primary" data-func="create-color">\
										<i class="fa fa-plus"></i> {$lumise->lang('Add new color')}\
									</button>\
									<button class="lumise-button" data-func="clear-color">\
										<i class="fa fa-eraser"></i> {$lumise->lang('Clear all')}\
									</button>\
									<textarea data-name="values" class="hidden">'+(values !== undefined ? values : '')+'</textarea>\
								</p>\
							</div>';
							
					wrp.html(content);
					
					if (typeof wrp.sortable == 'function') {
						wrp.find('ul.lumise-field-color').sortable({update: function() {
							var vals = [];
							$(this).find('li[data-color]').each(function() {
								vals.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));	
							});
							$(this).closest('.lumise-field-color-wrp').find('textarea[data-name="values"]').val(
								vals.join(decodeURI('%0A'))
							).trigger('change');
						}});
					};
					
					triggerObjects.general_events.return_colors = function(wrp) {
		
						var val = [];
						
						wrp.find('li[data-color]').each(function(){
							val.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));
						});
						
						val = val.join(decodeURI('%0A'));
						
						wrp.find('textarea[data-name="values"]').val(val).trigger('change');
							
					};
					
					trigger({
						el: wrp,
						events: {
							'button[data-func="create-color"]': 'add_color',
							'button[data-func="clear-color"]': 'clear_color',
							'ul.lumise-field-color': 'color_func'
						},
						add_color: function(e) {
							e.data = triggerObjects.general_events;
							triggerObjects.general_events.create_color(e);
						},
						clear_color: function(e) {
							$(this).closest('.att-layout-body-field').find('ul.lumise-field-color').html('');
							triggerObjects.general_events.return_colors(
								$(this).closest('.att-layout-body-field')
							);
							e.preventDefault();	
						},
						color_func: function(e) {
							if (
								e.target.getAttribute('data-func') == 'delete' ||
								e.target.getAttribute('data-color') == 'delete'
							) {
								$(e.target).parent().remove();
								triggerObjects.general_events.return_colors(
									$(this).closest('.att-layout-body-field')
								);
								e.preventDefault();	
							}	
						}
					});
					
EOF
				,'render' => <<<EOF
				
					var el = $('<ul class="lumise-product-color"></ul>'), valid_value = false;
					
					el.append('<li data-color="" title="{$lumise->lang('Clear color')}"></li>');
					
					data.values.map(function(v) {
						if (v.value !== '') {
							el.append('<li data-color="'+v.value+'" title="'+v.title.replace(/\"/g, '&quot;')+'" style="background-color:'+v.value+'"></li>');
							if (data.value === v.value)
								valid_value = true;
						}
					});
					
					el.append('<input type="hidden" name="'+data.id+'" class="lumise-cart-param" value="'+(valid_value ? data.value : '')+'" '+(data.required ? 'required' : '')+' />');
					
					el.find('li[data-color]').on('click', function(e) {
						$(this).parent().find('li.choosed').removeClass('choosed');
						$(this).addClass('choosed')
							   .closest('.lumise_form_content')
							   .find('input.lumise-cart-param')
							   .val(this.getAttribute('data-color'))
							   .trigger('change');
						e.preventDefault();
					});
					
					if (valid_value && data.value !== undefined && data.value !== '')
						el.find('li[data-color="'+data.value+'"]').trigger('click');
					
					return el;
						
EOF
			),
			
			'input' => array(
				'label' => $lumise->lang('Input text'),
				'default' => '',
				'placeholder' => '',
				'render' => <<<EOF
					return '<input type="text" name="'+data.id+'" class="lumise-cart-param" value="'+data.value+'" '+(data.required ? 'required' : '')+' />';			
EOF
			),
			
			'text' => array(
				'label' => $lumise->lang('Textarea'),
				'default' => '',
				'placeholder' => '',
				'render' => <<<EOF
					return '<textarea type="text" name="'+data.id+'" class="lumise-cart-param" '+(data.required ? 'required' : '')+'>'+data.value.replace(/\>/g, '&gt;').replace(/\</g, '&lt;')+'</textarea>';			
EOF
			),
			
			'checkbox' => array(
				'label' => $lumise->lang('Multiple checkbox'),
				'render' => <<<EOF
					
					var wrp = $('<div class="lumise_checkboxes"></div>');
					
					if (!data.value)
						data.value = [];
					else if (typeof data.value == 'string')
						data.value = data.value.split(decodeURI("%0A"));
					
					data.values.map(function(op) {
						
						var new_op 	= '<div class="lumise_checkbox">';
						
						new_op 	+= '<input type="checkbox" name="'+data.id+'" class="lumise-cart-param action_check" value="'+op.value+'" id="'+(data.id + '-' +op.value)+'" '+(data.required ? 'required' : '')+' '+(data.value.indexOf(op.value) > -1 ? 'checked' : '')+' />';
						new_op 	+= '<label for="'+(data.id + '-' +op.value)+'" class="lumise-cart-option-label">'+
										op.title.replace(/\</g, '&lt;').replace(/\>/g, '&gt;')+
										'<em class="check"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="12px" height="14px" viewBox="0 0 12 13" xml:space="preserve"><path fill="#4DB6AC" d="M0.211,6.663C0.119,6.571,0.074,6.435,0.074,6.343c0-0.091,0.045-0.229,0.137-0.32l0.64-0.64 c0.184-0.183,0.458-0.183,0.64,0L1.538,5.43l2.515,2.697c0.092,0.094,0.229,0.094,0.321,0l6.13-6.358l0.032-0.026l0.039-0.037 c0.186-0.183,0.432-0.12,0.613,0.063l0.64,0.642c0.183,0.184,0.183,0.457,0,0.64l0,0l-7.317,7.592 c-0.093,0.092-0.184,0.139-0.321,0.139s-0.228-0.047-0.319-0.139L0.302,6.8L0.211,6.663z"/></svg></em>'+
										'</label>';
									
						new_op 	+= '<em></em></div>';
										
						wrp.append(new_op);
						
					});
					
					return wrp;
					
EOF
			),

			'radio' => array(
				'label' => $lumise->lang('Radio checkbox'),
				'render' => <<<EOF
					
					var wrp = $('<div class="lumise_radios"></div>');
					
					if (!data.value)
						data.value = [];
					else if (typeof data.value == 'string')
						data.value = data.value.split(',');
					
					data.values.map(function (op){
						
						new_op 	= $('<div class="lumise-radio">'+
									'<input type="radio" class="lumise-cart-param" name="'+data.id+'" value="'+op.value+'" id="'+data.id+' '+op.value+'"'+(data.value.indexOf(op.value) > -1 ? ' checked' : '')+' />'+
				                	'<label class="lumise-cart-option-label" for="'+data.id+' '+op.value+'">'+op.title+' <em class="check"></em></label>'+
									'<em class="lumise-cart-option-desc"></em>'+
								'</div>');
						
						wrp.append(new_op);
						
					});
						
					return wrp;
								
EOF
			),
			
			
		);
	}
}