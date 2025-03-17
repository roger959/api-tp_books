<div class="background-video">
        <video autoplay muted loop id="bg-video">
            <source src="Vidéo sans titre ‐ Réalisée avec Clipchamp (1).mp4" type="video/mp4">
        </video>
    </div>

    <style>
         /* Arrière-plan vidéo */
         .background-video, #bg-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        #bg-video {
            filter: brightness(0.4);
        }
    </style>