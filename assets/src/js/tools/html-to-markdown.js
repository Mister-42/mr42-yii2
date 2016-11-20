(function () {
	var input, output, gfm;

	function updateOutput() {
		output.value = toMarkdown(input.value, { gfm: gfm.checked });
	}

	document.addEventListener("DOMContentLoaded", function(event) {
		input = document.getElementById('input');
		output = document.getElementById('output');
		gfm = document.getElementById('gfm');

		input.addEventListener('input', updateOutput, false);
		input.addEventListener('keydown', updateOutput, false);
		gfm.addEventListener('change', updateOutput, false);

		updateOutput();
	});
})();
