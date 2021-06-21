<template>
    <div class="mt-5">

        <div class="movie-card" v-if="filmData.data">
            <div class="movie-header mainFilmDiv"
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
                        <label>Genres</label>
                        <span
                            v-for="index in filmData.data.genres"
                        > {{ index.name }},</span>
                    </div>
                    <div class="info-section"></div>
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
                                v-model="comment.comment"
                            ></textarea>
                                <span>{{ errors[0] }}</span>
                            </validation-provider>
                        </div>
                        <button type="submit" :disabled="invalid" class="btn btn-primary">
                            Comment
                        </button>
                    </form>
                </ValidationObserver>
                <div class="movie-info">
                    <div class="info-section">
                        <label>Comments</label>
                    </div><!--Description-->
                    <div class="info-section"></div>
                </div>
                <!-- comments container -->
                <div class="comment_block">
                    <!-- new comment -->
                    <div class="new_comment">

                        <!-- build comment -->
                        <ul class="user_comment">
                            <!-- start user replies -->
                            <li v-for="index in filmData.data.comments" >
                                <!-- the comment body -->
                                <div class="comment_body">
                                    <div class="replied_to">
                                        <p>
                                            <span class="user">{{ index.name }}:</span>
                                            {{ index.comment }}
                                        </p>
                                    </div>
                                </div>
                                <!-- comments toolbar -->
                                <div class="comment_toolbar">
                                    <!-- inc. date and time -->
                                    <div class="comment_details">
                                        <ul>
                                            <li><i class="fa fa-clock-o"></i> {{ index.created_at | moment("hh:mm A") }}</li>
                                            <li><i class="fa fa-calendar"></i> {{ index.created_at | moment("dddd, MMMM Do YYYY") }} </li>
                                            <li><i class="fa fa-pencil"></i> <span class="user">{{ index.user.name }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>

                    </div>
                </div>
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
                comment: null,
                film_id: null,
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
            this.comment.film_id = this.slug;
            this.addCommentRequest(this.comment).then((res) => {
                if (res.response !== undefined) {
                    this.$swal(res.response.data.message);
                    return true;
                }
                this.$swal(res.data.message);
                this.getFilmData(this.slug);
            });
        }
    }
};
</script>
