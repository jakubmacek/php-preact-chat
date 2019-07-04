<?php /** @var PlatesTemplateInterface $this */ ?>
<?php /** @var stdClass $model */ ?>
<?php /** @var \Chat\AuthenticationToken $authenticationToken */ ?>
<?php /** @var string[] $smileys */ ?>
<?php $this->layout('_layout') ?>

<h1>Chat: <?= $this->e($authenticationToken->getName()) ?> (#<?= $authenticationToken->getId() ?>)</h1>

<!--
Framework pouzity pro cast v prohlizeci je Prect (ale temer beze zmeny by to melo jit vymenit za React).
Pouzivam Rollup pro zkompilovani jednotlivych TypeScriptovych trid do vysledneho chat.bundle.js. Kdybych chtel pouzit framework Vue, tak se asi vyplati nasadit WebPack, ale z moji zkusenosti je WebPack prilis komplikovany a plny nejakych zakernych triku v konfiguraci.
Pri nasazeni do produkce klidne muzou zustat odkazy na CDN, pokud jsou ty CDN alespon ramcove duveryhodne. Ale pokud by vyvojovy tym udrzoval takto stovky projektu, tak vypadek CDN by jim dost zavaril. V takovem pripade lepe pomoci Rollup z node_modules vytvorit vlastni versi do adresare assets.
-->

<div id="chat"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/preact/8.4.2/preact.dev.js" integrity="sha256-Cu0KOQTfaBOQDf8xJ2750uXS4QnsGG5EqPpNmNMImsU=" crossorigin="anonymous"></script>
<link rel="stylesheet" type="text/css" href="/assets/chat.bundle.css" />
<script type="text/javascript" src="/assets/chat.bundle.js"></script>
<script type="text/javascript">
	Chat.run(document.getElementById('chat'), {
	    myId: <?= json_encode($authenticationToken->getId()) ?>,
		myName: <?= json_encode($authenticationToken->getName()) ?>,
		smileys: <?= json_encode($smileys) ?>,
        readMessagesUrl: '/chatReadMessages.php',
        sendMessageUrl: '/chatSendMessage.php'
    });
</script>
