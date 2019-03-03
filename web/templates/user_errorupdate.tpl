{-if $errnomatch-}
    {-#errnomatch#-}
{-elseif $errbadpass-}
    {-#errbadpasswd#-}
{-elseif $errupduser-}
    {-#terror#-}[{-$updstat-}] {-#errupdate#-} {-$desinventarUserId-}
{-elseif $noerrorupd-}
    {-#msgupdatesucc#-} {-$desinventarUserId-}
{-/if-}
