// bootstrap - Initializes Bootstrap and its dependencies.
import './bootstrap';
import 'bootstrap';
import {Carousel} from 'bootstrap';

// forms - Import custom form components for handling various form functionalities.
import registerForm from "./components/registerForm.js";
import profileForm from "./components/profileForm.js";
import projectForm from "./components/projectForm.js";
import commentForm from "./components/commentForm.js";
import projectRatingForm from "./components/projectRatingForm.js";
import uploadProjectImageForm from "./components/uploadProjectImageForm.js";

// presentation components - Import components for displaying UI elements dynamically.
import projectRatingSummary from "./components/projectRatingSummary.js";
import paginatedComments from "./components/paginatedComments.js";

// alpine - Import Alpine.js and additional custom Alpine components for reactive behavior.
import Alpine from 'alpinejs';
import toggleUserFavouriteProjectForm from "./components/toggleUserFavouriteProjectForm.js";
import uploadProfilePictureForm from "./components/uploadProfilePictureForm.js";
import resetPasswordForm from "./components/resetPasswordForm.js";
import changePasswordForm from "./components/changePasswordForm.js";
import loginForm from "./components/loginForm.js";
window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    // Initialize Alpine.js form components
    Alpine.data('registerForm', registerForm);
    Alpine.data('loginForm', loginForm);
    Alpine.data('editProfileForm', profileForm);
    Alpine.data('changePasswordForm', changePasswordForm);
    Alpine.data('resetPasswordForm', resetPasswordForm);
    Alpine.data('projectForm', projectForm);
    Alpine.data('commentForm', commentForm);
    Alpine.data('uploadProjectImageForm', uploadProjectImageForm);
    Alpine.data('projectRatingForm', projectRatingForm);
    Alpine.data('toggleUserFavouriteProjectForm', toggleUserFavouriteProjectForm);
    Alpine.data('uploadProfilePictureForm', uploadProfilePictureForm);

    // Initialize Alpine.js presentation components
    Alpine.data('projectRatingSummary', projectRatingSummary);
    Alpine.data('paginatedComments', paginatedComments);

    // Define a global Alpine store for managing shared state (like comments count).
    Alpine.store('comments', {
        count: 0 // Tracks the number of comments.
    });
});

// Event listener for DOMContentLoaded to manage modals and carousels in the UI.
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('projectImageLightbox');
    const carouselEl = document.getElementById('lightboxCarousel');

    // If modal or carousel element is missing, exit early.
    if (!modal || !carouselEl) {
        return;
    }

    // Add the show.bs.modal event listener to sync the carousel with the triggered image.
    modal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const index = parseInt(trigger.getAttribute('data-index') || 0);

        const carousel = Carousel.getOrCreateInstance(carouselEl);
        carousel.to(index);
    });
});

Alpine.start();
