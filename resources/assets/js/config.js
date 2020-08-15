const apiDomain = Laravel.apiDomain;
export const siteName = Laravel.siteName;
export const siteUrl = Laravel.siteUrl;

export const api = {
	login: apiDomain + '/authenticate',
	currentUser: apiDomain + '/user',
	
	storeItem: apiDomain + '/item',
	updateItemImages: apiDomain + '/item/update/images/',
	getItem: apiDomain + '/item/show/',
	updateItem: apiDomain + '/item/update',
	deleteImage: apiDomain + '/item/deleteImage/',
	deleteItem : apiDomain + '/item/deleteItem/',
	getItems: apiDomain + '/item',

};