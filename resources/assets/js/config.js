const apiDomain = Laravel.apiDomain;
export const siteName = Laravel.siteName;
export const siteUrl = Laravel.siteUrl;

export const api = {
	login: apiDomain + '/authenticate',
	currentUser: apiDomain + '/user',
	updateUserProfile: apiDomain + '/user/profile/update',
	updateUserPassword: apiDomain + '/user/password/update'
};