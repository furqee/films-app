/**
 * Load Dependencies and Modules
 */
import Vue from "vue";
import VueRouter from "vue-router";
import Login from "./components/Auth/Login";
import Register from "./components/Auth/Register";
import Profile from "./components/Auth/Profile";
import Verify from "./components/Auth/Verify";
import NotFoundComponent from "./components/NotFoundComponent";

/**
 * Inject Vue router in Vue
 */
Vue.use(VueRouter);

/**
 * Middlewares
 * @param to
 * @param from
 * @param next
 * @returns {*}
 */
const guest = (to, from, next) => {
    if (!localStorage.getItem("authToken")) {
        return next();
    } else {
        return next("/");
    }
};
const auth = (to, from, next) => {
    if (localStorage.getItem("authToken")) {
        return next();
    } else {
        return next("/login");
    }
};

const routes = [
    {
        path: "/login",
        name: "Login",
        beforeEnter: guest,
        component: Login,
    },
    {
        path: "/register",
        name: "Register",
        beforeEnter: guest,
        component: Register,
    },
    {
        path: "/verify/:hash",
        name: "Verify",
        beforeEnter: auth,
        props: true,
        component: Verify,
    },
    {
        path: "/profile",
        name: "Profile",
        beforeEnter: auth,
        component: Profile,
    },
    {
        path: '*',
        component: NotFoundComponent,
    }
];

/**
 * Set routes
 * @type {VueRouter}
 */
const router = new VueRouter({
    mode: "history",
    base: process.env.APP_URL,
    routes
});

export default router;
