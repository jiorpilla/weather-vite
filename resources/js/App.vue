<template>
<div class="bg-light">
    <header-component @search="searchPlaces"></header-component>

    <div class="container" v-if="venues.length">
        <div class="row">
            <div class="col-md-12 py-3 ">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Search: {{ city }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row mt-3">
            <div id="main" class="row">
                <div class="col-12 col-lg-8">
                    <div class="border rounded p-3 mb-5 bg-white">

                        <div class="d-flex justify-content-between">
                            <h3>{{ city }}, JP</h3>
                            <img :src="currentWeather.iconUrl">
                        </div>
                        <p class="small">{{ currentWeather.description }}</p>
                        <div class="row">
                            <div class="col-4">
                                <p>Temperature : <strong>{{ currentWeather.main.temp }}&deg;c</strong></p>
                            </div>
                            <div class="col-4">
                                <p>Feels like : <strong>{{ currentWeather.main.feels_like }}&deg;c</strong></p>
                            </div>
                            <div class="col-4">
                                <p>Humidity : <strong>{{ currentWeather.main.humidity }}</strong></p>
                            </div>
                            <div class="col-4">
                                <p>Min Temperature : <strong>{{ currentWeather.main.temp_min }}&deg;c</strong></p>
                            </div>
                            <div class="col-4">
                                <p>Max Temperature : <strong>{{ currentWeather.main.temp_max }}&deg;c</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <p>Wind Speed : <strong>{{ currentWeather.wind.speed }}km/hr</strong></p>
                            </div>
                            <div class="col-4">
                                <p>Wind Degree : <strong>{{ currentWeather.wind.speed }}km/hr</strong></p>
                            </div>
                            <div class="col-4">
                                <p>Wind Gust : <strong>{{ currentWeather.wind.gust }}</strong></p>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div v-if="venues.length">
                            <div class="row">
                                <div class="col-12 col-lg-6" v-for="result in venues" :key="result.id">
                                    <div class="card bg-dark text-white mb-3">
                                        <img :src="result.imageUrl" class="card-img" alt="{{ result.name }}">
                                        <div class="card-img-overlay bg-overlay d-flex flex-column justify-content-between">

                                            <div class="card-header">
                                                <span class="badge text-bg-secondary"><img :src="result.iconUrl" /></span>
                                            </div>
                                            <div class="card-body py-5">
                                                <h5 class="">{{ result.name }}</h5>
                                            </div>
                                            <div class="card-footer">
                                                <p class="small badge bg-success">{{ result.categoryName }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-12 col-lg-4" v-if="venues.length">
                    <div class="border rounded p-3 mb-5 bg-white">
                        <div class="d-flex flex-column justify-content-between gap-2">
                            <h4>Weather forecast in {{city}}</h4>
                            <div v-for="(item, key) in forecast" :key="key">
                                <div class="d-flex justify-content-center align-content-center">
                                    <div class="bg-success-subtle text px-4 py-3 rounded w-100">
                                        <div class="d-flex justify-content-between">
                                            <h4>{{ item.date_text }}</h4>
                                            <img :src="item.iconUrl">
                                        </div>
                                        <p class=""><em>{{ item.description }}</em></p>

                                        <div class="row">
                                            <p class="col-6 small">Temperature: <strong>{{ item.main.temp }}</strong></p>
                                            <p class="col-6 small">Humidity: <strong>{{ item.main.humidity }}%</strong></p>
                                            <p class="col-12 small">Wind Speed: <strong>{{ item.wind.speed }} m/s</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else-if="loading">
        <div class="d-flex justify-content-center py-5">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <div class="container h-75" v-else>
        <div class="mt-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="border rounded p-3 mb-5 bg-white">
                        Please search for a city in Japan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer-component></footer-component>
</div>
</template>
<script>

import HeaderComponent from './components/HeaderComponent.vue'
import FooterComponent from './components/FooterComponent.vue'

export default {
    components: {
        HeaderComponent,
        FooterComponent
    },
    data() {
        return {
            venues: [],
            city: '',
            currentWeather: [],
            forecast: '',
            loading: false,
        };
    },
    methods: {
        searchPlaces(search) {
            this.loading = true;
            this.city = search.substring(0, 1).toUpperCase() + search.substring(1) + ' City';
            axios.get(`/api/place-search/${search}`)
                .then(response => {
                    this.venues = response.data.venues;
                    this.currentWeather = response.data.city_weather;
                    this.forecast = response.data.city_forecast;
                    this.loading = false;
                })
                .catch(error => {
                    console.error(error);
                    this.loading = false;
                });
        }
    }
}
</script>
