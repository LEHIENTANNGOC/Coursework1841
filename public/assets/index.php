<?php

if (!defined('_TanHien')) {
    die('Access denied');
}


function handleCoverImageChange(input) {
if (input.files && input.files[0]) {
    const fileName = input.files[0].name;
    const button = document.querySelector('.add-cover-btn');
    button.innerHTML = `
        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
        </svg>
        Cover image selected: ${fileName}
    `;
    button.style.color = '#10b981';
    button.style.borderColor = '#10b981';
}
}