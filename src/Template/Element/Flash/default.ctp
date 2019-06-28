<script>
    $(function(){
		new PNotify({
			text: '<?= $message?>',
			type: 'info',
			styling: 'bootstrap3',
			width:'280px'
        });
    });
</script>