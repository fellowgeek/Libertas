<?php
include( dirname(__FILE__).'/core/dbug.php');

$text = <<<BLOCK
Fap flannel fashion axe tofu, food truck squid leggings quis seitan biodiesel try-hard retro letterpress put a bird on it culpa.  Semiotics meggings minim gentrify whatever.  Duis  sustainable sint  direct trade  fashion axe 3 wolf moon, DIY Carles nesciunt.  Ut lo-fi church-key Bushwick reprehenderit  raw denim narwhal, pug Brooklyn Odd Future magna readymade.  Dreamcatcher assumenda Terry Richardson eu, beard salvia authentic laborum chillwave consequat nisi ethical small batch.  Kale chips aliqua trust fund, sapiente wayfarers freegan Terry Richardson Schlitz squid banh mi officia.  Keffiyeh retro mixtape next level, blog Intelligentsia sint  Shoreditch.

[file:sample.pdf|width=100%|height=282|class=polaroid|Description=Image of a GOAT|alignment=left|Alt=Image of a GNU GOAT|data-target=meeho]
Synth dreamcatcher street art laboris.  Quis blog jean shorts, locavore VHS sapiente direct trade  8-bit Williamsburg sint  irure  sartorial.  Stumptown reprehenderit  messenger bag twee church-key keytar dolore, disrupt esse.  Authentic McSweeney's food truck, velit  lomo shabby chic farm-to-table fashion axe officia  eu  twee nesciunt.  Williamsburg craft beer ex chambray, deserunt  duis  keytar dreamcatcher fingerstache pug dolore Austin sapiente pop-up.  Kogi butcher McSweeney's Thundercats.  Letterpress DIY Neutra iPhone authentic, keffiyeh sunt chillwave 90's Thundercats Pitchfork.

Odio McSweeney's slow-carb reprehenderit  Vice nostrud 3 wolf moon 8-bit Godard, organic est  Marfa ad meh selfies.  Narwhal non  yr gentrify Thundercats salvia messenger bag brunch, Echo Park fugiat  pickled Brooklyn disrupt Shoreditch.  Aliqua laborum fingerstache occupy butcher, quis excepteur  ea occaecat.  Kitsch synth PBR tote bag, irony commodo Schlitz fixie mlkshk deep v.  Semiotics dreamcatcher nisi, ullamco photo booth four loko adipisicing.  Laboris  Tonx tattooed, messenger bag pariatur pour-over kitsch fashion axe cillum  id  gentrify paleo.  Pinterest Helvetica keffiyeh, Shoreditch Marfa roof party anim  id  next level narwhal fingerstache DIY in  church-key.



===VHS Echo Park, Assumenda Irony===
Banjo fashion axe retro, nisi ennui synth occaecat  irony vero Schlitz proident before they sold out Godard incididunt.  Cillum  banjo cornhole meh gluten-free kitsch.  Cardigan dolor  before they sold out Marfa letterpress, enim Banksy.  Culpa  single-origin coffee consectetur, try-hard twee biodiesel accusamus.  Mustache meh trust fund, sunt chambray pariatur deserunt  yr Truffaut aesthetic selfies post-ironic Pitchfork swag Bushwick.  Vero pork belly yr assumenda.  IPhone enim Tonx scenester photo booth
cupidatat, craft beer mumblecore pariatur cardigan put a bird on it organic.


Ugh small batch VHS Echo Park, assumenda irony keytar Godard adipisicing meh farm-to-table +1 Helvetica Marfa irure.  Do chambray ad asymmetrical, placeat incididunt High Life dolore banjo delectus gluten-free.  Farm-to-table aliqua American Apparel, quis officia  salvia pug gluten-free Pinterest ennui.  Hashtag Neutra iPhone consectetur freegan, retro sustainable +1 kitsch Brooklyn velit.  Church-key try-hard fixie, Portland cardigan non  deserunt  ennui paleo Thundercats Shoreditch minim est.  Twee asymmetrical Portland brunch.  Pinterest elit delectus, proident et vero sunt PBR brunch accusamus.

BLOCK;

define('__SITE__', 'libertas.erfan.me');

$protocol = 'http://';


		// process components [C:COMPONENT|Param1=Value|Param2=Value|...]
		function process_components($text, $path, $protocol) {

			$count = preg_match_all("@\[C:(.*?)\]@i", $text, $matches);

			if(isset($matches[1]) == TRUE && $count > 0) {
				$i = 0;
				foreach($matches[1] as $component) {

					$component = strtolower($component);
					$params = array();

					if(is_file(__SELF__ . 'components/' . $component . '/' . $component . '.php') == TRUE) {
						// creating component object
						$oobject_component = $this->route('/com/' . $component . '/' . $component . '/');
						$oobject_component->name = $component;
						$oobject_component->views = __SELF__ . 'components/' . $component . '/views/';
						// start output buffering and call the output (out) method
						ob_start();
						$oobject_component->out($path, $protocol, $params);
						$component_html = ob_get_clean();

						// scan component's assets folder for css & js files
						if(is_dir(__SELF__ . 'components/' . $component . '/css/') == TRUE) {
							$files = scandir(__SELF__ . 'components/' . $component . '/css/');
							foreach($files as $file) {
								if($file != '.' && $file != '..' && preg_match("@^(.*?)\.css$@", $file) != FALSE) {
									$_SESSION["cms"]["css"][] = '<link href="' . $protocol . __SITE__ . '/components/' . $component . '/css/' . $file . '" rel="stylesheet">';
								}
							}
						}

						if(is_dir(__SELF__ . 'components/' . $component . '/js/') == TRUE) {
							$files = scandir(__SELF__ . 'components/' . $component . '/js/');
							foreach($files as $file) {
								if($file != '.' && $file != '..' && pathinfo($file, PATHINFO_EXTENSION) == 'js') {
									$_SESSION["cms"]["js"][] = '<script src="' . $protocol . __SITE__ . '/components/' . $component . '/js/' . $file . '"></script>';
								}
							}
						}

					} else {
						$component_html = '<br style="clear: both;" /><img src="/files/missing.png" title="Missing Component: ' . $component . '"/><br style="clear: both;" />';
					}

					$text = str_ireplace($matches[0][$i], $component_html, $text);
					$i++;
				}
			}
			unset($matches);

			return $text;
		}


print("<pre>");
print($text);
print("</pre>");
?>