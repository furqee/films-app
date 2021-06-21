<template>
    <div class="mt-5">

        <div class="movie-card" v-if="filmData.data">
            <div class="movie-header manOfSteel"
                 v-bind:style="{ backgroundImage: 'url(/storage/film-images/' + filmData.data.photo + ')' }">
                <div class="header-icon-container">
                    <a href="#">
                        <i class="material-icons header-icon"></i>
                    </a>
                </div>
            </div><!--movie-header-->
            <div class="movie-content">
                <div class="movie-content-header">
                    <a href="#">
                        <h3 class="movie-title">{{ filmData.data.name }}</h3>
                    </a>
                </div>
                <div class="movie-info">
                    <div class="info-section">
                        <label>Release Date</label>
                        <span>{{ filmData.data.release_date | moment("dddd, MMMM Do YYYY") }}</span>
                    </div><!--date,time-->
                    <div class="info-section">
                        <label>Rating</label>
                        <span class="fa fa-star"
                              v-bind:class="{ checked: index <= filmData.data.rating }"
                              v-for="index in 5"
                              :key="index"
                        ></span>
                    </div><!--screen-->
                </div>
                <div class="movie-info">
                    <div class="info-section">
                        <label>Description</label>
                        <span>{{ filmData.data.description }}</span>
                    </div><!--Description-->
                    <div class="info-section"></div>
                </div>
                <div class="movie-info">
                    <div class="info-section">
                        <label>Price</label>
                        <span>${{ filmData.data.price }}</span>
                    </div><!--Description-->
                    <div class="info-section"></div>
                </div>
                <div class="movie-info">
                    <div class="info-section">
                        <label>Country</label>
                        <span>{{ filmData.data.country.name }}</span>
                    </div><!--Description-->
                    <div class="info-section"></div>
                </div>
                <ValidationObserver v-slot="{ invalid }">
                    <form @submit.prevent="create">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <validation-provider rules="required|min:5" v-slot="{ errors }">
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': errors.name }"
                                    id="name"
                                    placeholder="Enter film name"
                                    v-model="comment.name"
                                />
                                <span>{{ errors[0] }}</span>
                            </validation-provider>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <validation-provider rules="required|min:5" v-slot="{ errors }">
                            <textarea
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': errors.description }"
                                id="description"
                                placeholder="Enter film description"
                                v-model="comment.text"
                            ></textarea>
                            <span>{{ errors[0] }}</span>
                            </validation-provider>
                        </div>
                        <button type="submit" :disabled="invalid" class="btn btn-primary">
                            Comment
                        </button>
                    </form>
                </ValidationObserver>
            </div><!--movie-content-->
        </div><!--movie-card-->

    </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";

export default {
    name: "Detail",

    props: ["slug"],

    data() {
        return {
            comment: {
                name: null,
                text: null
            }
        };
    },
    mounted() {
        this.getFilmData(this.slug);
    },
    computed: {
        ...mapGetters("film", ["filmData"])
    },

    methods: {
        ...mapActions("film", ["getFilmData", "addCommentRequest"]),

        create() {
            this.addCommentRequest(data).then(() => {
                this.$router.push({name: "Films"});
            });
        }
    }
};
</script>
