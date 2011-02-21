<?php
if ($isAjax) {
	echo $this->Js->writeBuffer();
}
?>
    
<?php if ($ajaxFrame == 'body'): ?>
    </div>
    </div>
    <div class="clear"></div>
    <!-- fields required for history management -->
    <form id="history-form" class="x-hidden">
        <input type="hidden" id="x-history-field" />
        <iframe id="x-history-frame"></iframe>
    </form>
<?php endif; ?>
