<script>
    $(function(){
		new PNotify({
			title: '成功',
			text: '<?= $message?>',
			type: 'success',
			styling: 'bootstrap3',
			delay: 3000,
			width:'280px'
        });
    });
</script>
