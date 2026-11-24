<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const guaranteeEndDate = document.getElementById('performance_guarantee_end_date');

        if (!startDate) {
            return;
        }

        const endBeforeStartMessage = 'የመጨረሻ ቀን ከመጀመሪያ ቀን በፊት መሆን የለበትም።';
        const guaranteeBeforeStartMessage = 'የዋስትና ማብቂያ ቀን ከውል መጀመሪያ ቀን በፊት መሆን የለበትም።';

        function showInlineError(element, message) {
            let errorDiv = element.nextElementSibling;

            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                element.parentNode.insertBefore(errorDiv, element.nextSibling);
            }

            errorDiv.textContent = message;
            element.classList.add('is-invalid');

            setTimeout(function () {
                errorDiv.textContent = '';
                element.classList.remove('is-invalid');
            }, 3000);
        }

        function applyMinimum(element, minimum) {
            if (!element) {
                return;
            }

            if (minimum) {
                element.min = minimum;
            } else {
                element.removeAttribute('min');
            }
        }

        function isBeforeStart(element) {
            return Boolean(element && startDate.value && element.value && element.value < startDate.value);
        }

        function syncRelatedDates(notify) {
            const selectedDate = startDate.value;

            applyMinimum(endDate, selectedDate);
            applyMinimum(guaranteeEndDate, selectedDate);

            if (!notify) {
                return;
            }

            if (isBeforeStart(endDate)) {
                endDate.value = '';
                showInlineError(endDate, endBeforeStartMessage);
            }

            if (isBeforeStart(guaranteeEndDate)) {
                guaranteeEndDate.value = '';
                showInlineError(guaranteeEndDate, guaranteeBeforeStartMessage);
            }
        }

        startDate.addEventListener('change', function () {
            syncRelatedDates(true);
        });

        const form = document.getElementById('contractForm');
        if (form) {
            form.addEventListener('submit', function (event) {
                if (isBeforeStart(endDate)) {
                    event.preventDefault();
                    showInlineError(endDate, endBeforeStartMessage);
                }

                if (isBeforeStart(guaranteeEndDate)) {
                    event.preventDefault();
                    showInlineError(guaranteeEndDate, guaranteeBeforeStartMessage);
                }
            });
        }

        // The create form fills default dates after this listener is registered.
        setTimeout(function () {
            syncRelatedDates(false);
        }, 0);
    });
</script>
