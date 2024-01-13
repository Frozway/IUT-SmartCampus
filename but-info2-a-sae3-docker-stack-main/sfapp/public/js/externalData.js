const container = document.querySelector('.container');
const search = document.querySelector('.search-box button');
const weatherBox = document.querySelector('.weather-box');
const error404 = document.querySelector('.not-found');

const performSearch = () => {
    const APIKey = 'bef0f873bd6633be3fbd81bedb9a02be';
    city = document.querySelector('.search-box input').value;


    //Si aucune ville n'est encore renseigné, on assigne par défaut la ville de La Rochelle
    if (city === '') {
        city = 'La Rochelle';
    }

    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&units=metric&appid=${APIKey}&lang=fr`)
        .then(response => response.json())
        .then(json => {


            if (json.cod === '404') {
                console.log('error');
                weatherBox.style.display = 'none';
                error404.style.display = 'block';
                error404.classList.add('fadeIn');
                return;
            }

            error404.style.display = 'none';
            error404.classList.remove('fadeIn');

            const image = document.querySelector('.weather-box img');
            const temperature = document.querySelector('.weather-box .temperature');
            const description = document.querySelector('.weather-box .description');
            const location = document.querySelector('.weather-box .location');


            switch (json.weather[0].main) {
                case 'Clear':
                    image.src = '/images/weather/clear.png';
                    break;

                case 'Rain':
                    image.src = '/images/weather/rain.png';
                    break;

                case 'Snow':
                    image.src = '/images/weather/snow.png';
                    break;

                case 'Clouds':
                    image.src = '/images/weather/cloud.png';
                    break;

                case 'Mist':
                    image.src = '/images/weather/mist.png';
                    break;

                default:
                    image.src = '';
            }

            temperature.innerHTML = `${parseInt(json.main.temp)}<span>°C</span>`;
            description.innerHTML = `${json.weather[0].description}`;
            location.innerHTML = `${json.name}`;

            weatherBox.style.display = '';
            weatherBox.classList.add('fadeIn');

        });

    weatherBox.classList.remove('fadeIn');
    
};

// Lancer performSearch au chargement de la page
performSearch();

// Exectuer la recherche au click sur la touche entrée
document.querySelector('.search-box').addEventListener('keypress', e => {
    if (e.keyCode === 13)
        performSearch();
});

// Écoute de l'événement click du bouton de recherche
search.addEventListener('click', performSearch);



