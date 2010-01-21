{-if $errnomatch-}
	{-#errnomatch#-}
{-elseif $errbadpass-}
	{-#errbadpasswd#-}
{-elseif $errupduser-}
	{-#terror#-}[{-$updstat-}] {-#errupdate#-} {-$userid-}
{-elseif $noerrorupd-}
	{-#msgupdatesucc#-} {-$userid-}
{-/if-}
