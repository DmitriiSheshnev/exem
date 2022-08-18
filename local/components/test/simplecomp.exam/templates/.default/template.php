<?
if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();
use Bitrix\Main\Localization\Loc;

//echo '<pre>';print_r($arResult["ITEMS"]);echo '</pre>';
?>
<?if($arResult["ITEMS"]):?>
	<ul>
	<?foreach($arResult["ITEMS"] as $item):?>
		<li>
			<p><b><?=$item['NAME']?></b><?=$item['ACTIVE_FROM'] ? ' - '.$item['ACTIVE_FROM'] : ''?><?=$item['SECTIONS_NAMES'] ? ' ('.implode(', ', $item['SECTIONS_NAMES']).')' : ''?></p>
			<?if($item['SECTIONS']):?>
				<?foreach($item['SECTIONS'] as $section):?>
					<?if($section['ELEMENTS']):?>
						<ul>
							<?foreach($section['ELEMENTS'] as $element):?>
								<?
									$arrName = [$element['NAME']];
									foreach($element['PROPS'] as $prop){
										$arrName[] = $prop;
									}
								?>
								<li><?=implode(' - ', $arrName)?></li>
							<?endforeach;?>
						</ul>
					<?endif;?>
				<?endforeach;?>
			<?endif;?>
		</li>
	<?endforeach;?>
	</ul>
<?endif;?>