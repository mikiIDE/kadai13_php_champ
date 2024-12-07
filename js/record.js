function toggleStepCount(show) {
    const stepCountGroup = document.getElementById('step-count-group');
    const stepCountInput = stepCountGroup.querySelector('input');
    
    if (show) {
        stepCountGroup.classList.add('visible');
        stepCountInput.required = true;
    } else {
        stepCountGroup.classList.remove('visible');
        stepCountInput.required = false;
        stepCountInput.value = '';
    }
}