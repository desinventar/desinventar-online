{-* Geography List/Tree *-}
<ul id="tree-geotree" {-if $maintree == "true"-}class="checktree"{-/if-}>
{-foreach name=geol key=key item=item from=$geol-}
	<li id="show-{-$key-}">
		<input type="checkbox" id="{-$key-}" name="D_GeographyId[]" value="{-$key-}"
			onClick="setSelMap('{-$item[0]-}', '{-$key-}', this.checked);" {-if $item[3]-}checked{-/if-} />
		<label for="{-$key-}">{-$item[1]-}</label>
		<span id="itree-{-$key-}" class="count"></span>
	</li>
{-/foreach-}
</ul>
{-if $gtree-}
		<ul id="tree-geotree" class="checktree">
{-foreach from=$gtree key=key item="subtree"-}
{-assign var="geo" value="|"|explode:$key-}
		<li id="show-{-$geo[0]-}">
			<input type="checkbox" id="{-$geo[0]-}" name="D_GeographyId[]" value="{-$geo[0]-}" {-if $geo[2]-}checked{-/if-}/>
			<label for="{-$geo[0]-}">{-$geo[1]-}</label>
			<span id="itree-{-$geo[0]-}" class="count"></span>
{-include file="gtree.tpl" gtree=$subtree-}
		</li>
{-/foreach-}
		</ul>
{-/if-}
