<script>
(function () {
    var startInput = document.getElementById('start_date');
    var endInput = document.getElementById('end_date');
    var form = document.getElementById('contractForm');

    if (!startInput || !endInput) return;

    function guaranteeInput() {
        return document.getElementById('performance_guarantee_end_date');
    }

    var beforeStartMessage = 'የመጨረሻ ቀን ከመጀመሪያ ቀን በፊት መሆን የለበትም።';
    var sequenceMessage = 'የቀን ቅደም ተከተል ትክክል አይደለም። እባክዎ የመጨረሻ ቀን ከመጀመሪያ ቀን በኋላ ያስገቡ።';

    function applyMinimum(input, minimum) {
        if (!input) return;

        if (minimum) {
            input.min = minimum;
        } else {
            input.removeAttribute('min');
        }
    }

    applyMinimum(endInput, startInput.value);
    applyMinimum(guaranteeInput(), startInput.value);

    // The create form fills default dates on DOMContentLoaded, after this script runs.
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            if (startInput.value) {
                applyMinimum(endInput, startInput.value);
                applyMinimum(guaranteeInput(), startInput.value);
            }
        }, 0);
    });

    startInput.addEventListener('change', function () {
        endInput.min = this.value;
        var guarantee = guaranteeInput();
        if (guarantee) guarantee.min = this.value;

        if (this.value && endInput.value && endInput.value < this.value) {
            endInput.value = '';
            alert(beforeStartMessage);
        }
    });

    endInput.addEventListener('change', function () {
        if (this.value && startInput.value && this.value < startInput.value) {
            this.value = '';
            alert(beforeStartMessage);
        }
    });

    if (form) {
        form.addEventListener('submit', function (event) {
            if (startInput.value && endInput.value && startInput.value > endInput.value) {
                event.preventDefault();
                event.stopImmediatePropagation();
                alert(sequenceMessage);
            }
        });
    }
})();
</script>
