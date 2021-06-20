<template>
    <div class="login mt-5">
        <div class="card">
            <div class="card-header">
                Login
            </div>
            <div class="card-body">
                <ValidationObserver v-slot="{ invalid }">
                    <form @submit.prevent="onSubmit">
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <validation-provider rules="required|email" v-slot="{ errors }">
                            <input
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': errors.email }"
                                id="email"
                                v-model="details.email"
                                placeholder="Enter email"
                            />
                                <span>{{ errors[0] }}</span>
                            </validation-provider>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <validation-provider rules="required|min:5" v-slot="{ errors }">
                            <input
                                type="password"
                                class="form-control"
                                :class="{ 'is-invalid': errors.password }"
                                id="password"
                                v-model="details.password"
                                placeholder="Password"
                            />
                                <span>{{ errors[0] }}</span>
                            </validation-provider>
                        </div>
                        <button type="submit" :disabled="invalid" class="btn btn-primary">
                            Login
                        </button>
                    </form>
                </ValidationObserver>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions} from "vuex";

export default {
    name: "Login",

    data: function () {
        return {
            details: {
                email: null,
                password: null
            }
        };
    },

    methods: {
        ...mapActions("auth", ["sendLoginRequest"]),

        onSubmit() {
            this.sendLoginRequest(this.details).then((res) => {
                if (res.response !== undefined) {
                    this.$swal(res.response.data.message);
                    return true;
                }
                this.$swal(res.data.message);
            });
        }
    }
};
</script>
