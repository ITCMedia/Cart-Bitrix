<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo '<pre>';print_r($arResult); echo '</pre>';

if(is_array($arResult['ITEMS']) && ($cnt = count($arResult['ITEMS']))>0) {?>
<div class="catalogList">
	<?$i=0; $count = 3;
	foreach($arResult['ITEMS'] as $item){
		?>
		<div class="item prod<?=$item['ID']?>">
			<div class="image">
			<a href="<?=$item['DETAIL_PAGE_URL']?>">
			<?if(isset($item['IMAGE']) && is_array($item['IMAGE']) && isset($item['IMAGE']['SRC']) && strlen($item['IMAGE']['SRC'])>0){?>
				<img src="<?=$item['IMAGE']['SRC']?>" alt="<?=$item['NAME']?>"/>
			<?}else{?>
				<img src="<?=$templateFolder?>/images/no_photo.jpg" alt="<?=$item['NAME']?>"/>
			<?}?>
			</a>
			</div>
			<div class="desc">
				<div class="rightDesc">
					<div class="avalible"></div>
					<?if(isset($arResult["PRICE"][$item["ID"]]["DISPLAY"])){?>
						<div class="price">
						<?if($arResult["PRICE"][$item["ID"]]["VALUE"]>0){?>
						<?=$arResult["PRICE"][$item["ID"]]["DISPLAY"]?>
						<?}else{?>
						<?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_1");?>
						<?}?>
						</div>
						<?if($arResult["PRICE"][$item["ID"]]["VALUE"]>0){?>
						<div class="addToCart">
							<a href="#" data-id="<?=$item["ID"]?>"><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_2");?></a>
						</div>
						<?}?>
					<?}?>
				</div>
				<div class="leftDesc">
					<div class="name"><a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a></div>
					<div class="text">
					<?if($item['PREVIEW_TEXT']){?>
						<?=$item['PREVIEW_TEXT']?>
					<?}else{?>
						<?
						foreach($item["PROP"] as $prop){
							if(in_array($prop["CODE"],$arParams["PROPERTY_CODE"]) && $prop["VALUE"]){
								if(is_array($prop["VALUE"]) && count($prop["VALUE"])>0){
									echo $prop["NAME"].": ".implode(", ",$prop["VALUE"])."; ";
								}elseif(!is_array($prop["VALUE"]) && strlen($prop["VALUE"])){
									if(mb_strtolower($prop["VALUE"])=="да") {
										echo $prop["NAME"]."; ";
									}else{
										echo $prop["NAME"].": ".$prop["VALUE"]."; ";
									}
								}
							}
						}
						?>
					<?}?>
					</div>
					<div class="readmore"><a href="<?=$item['DETAIL_PAGE_URL']?>"><?=GetMessage("MLIFE_ASZ_CATALOG_SECTION_T_3");?></a></div>
				</div>
				
				
			</div>
		</div>
		<?
	}
	?>
</div>
<div class="nav">
<?echo $arResult["NAV_STRING"];?>
</div>
	<?
}

?>