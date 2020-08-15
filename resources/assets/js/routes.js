import Login from './components/login/Login.vue';
import ProfileWrapper from './components/profile/ProfileWrapper.vue';
import Profile from './components/profile/Profile.vue';

import Form from './components/Form/FormWrapper.vue';
import AddForm from './components/Form/Add.vue';
import EditForm from './components/Form/Edit.vue';

export default [
	{
		path: '/',
		name: 'index',
		component: Login,
		meta: {requiresGuest: true}
	},
	{
		path: '/form',
		name: 'form',
		component: Form,
		meta: {requiresAuth: true}
	},
	{
		path: '/form/new',
		name: 'form.Add',
		component: AddForm,
		meta: {requiresAuth: true}
	},
	{
		path: '/form/edit/:id',
		name: 'form.Edit',
		component: EditForm,
		meta: {requiresAuth: true}
	},
	{
		path: '/profile',
		component: ProfileWrapper,
		children: [
			{
				path: '',
				name: 'profile',
				component: Profile,
				meta: {requiresAuth: true}
			},
			{
				path: '*',
				redirect: {
					name: 'profile'
				}
			}
		]
	}
];