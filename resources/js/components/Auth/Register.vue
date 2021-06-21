<template>
    <div class="login mt-5">
        <div class="card">
            <div class="card-header">
                Register
            </div>
            <div class="card-body">
                <ValidationObserver v-slot="{ invalid }">
                <form @submit.prevent="onSubmit">
                    <div class="form-group">
                        <label for="email">Name</label>
                        <validation-provider rules="required|min:5" v-slot="{ errors }">
                            <input
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': errors.name }"
                                id="name"
                                v-model="user.name"
                                placeholder="Enter name"
                            />
                            <span>{{ errors[0] }}</span>
                        </validation-provider>
                    </div>
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <validation-provider rules="required|email" v-slot="{ errors }">
                            <input
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': errors.email }"
                                id="email"
                                v-model="user.email"
                                placeholder="Enter email"
                            />
                            <span>{{ errors[0] }}</span>
                        </validation-provider>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <validation-provider rules="required|confirmed:confirmation|min:5" v-slot="{ errors }">
                            <input
                                type="password"
                                class="form-control"
                                :class="{ 'is-invalid': errors.password }"
                                id="password"
                                v-model="user.password"
                                placeholder="Password"
                            />
                            <span>{{ errors[0] }}</span>
                        </validation-provider>
                    </div>
                    <div class="form-group">
                        <label for="c_password">Confirm password</label>
                        <validation-provider rules="required" v-slot="{ errors }" vid="confirmation">
                            <input
                                type="password"
                                class="form-control"
                                id="c_password"
                                v-model="user.c_password"
                                placeholder="Confirm password"
                            />
                            <span>{{ errors[0] }}</span>
                        </validation-provider>
                    </div>
                    <button type="submit" :disabled="invalid" class="btn btn-primary">
                        Register
                    </button>
                </form>
                </ValidationObserver>
            </div>
        </div>
    </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
    name: "Register",

    data: function () {
        return {
            user: {
                name: null,
                email: null,
                password: null,
                c_password: null
            }
        };
    },

    methods: {
        ...mapActions("auth", ["sendRegisterRequest"]),

        onSubmit() {
            this.sendRegisterRequest(this.user).then((res) => {
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
