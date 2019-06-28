<script>
    $(function(){
		new PNotify({
			title: '錯誤',
			text: '<?= $message?>',
			type: 'error',
			styling: 'bootstrap3',
			delay: 3000,
			width:'280px'
        });
    });
</script>