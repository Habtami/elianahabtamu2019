<script>
(function () {
    var startInput = document.getElementById('start_date');
    var endInput = document.getElementById('end_date');
    if (!startInput || !endInput) return;

    var message = 'Invalid date sequence. Please enter a date after the previous date.';
    var feedback = document.getElementById('endDateSequenceError');

    function refresh() {
        var minimum = startInput.value;

        if (minimum) {
            endInput.min = minimum;
        } else {
            endInput.removeAttribute('min');
        }

        var outOfOrder = Boolean(minimum && endInput.value && endInput.value <= minimum);
        endInput.setCustomValidity(outOfOrder ? message : '');

        if (feedback) {
            feedback.textContent = outOfOrder ? message : '';
            feedback.classList.toggle('hidden', !outOfOrder);
        }
    }

    [startInput, endInput].forEach(function (input) {
        input.addEventListener('change', refresh);
        input.addEventListener('input', refresh);
    });

    refresh();
})();
</script>
