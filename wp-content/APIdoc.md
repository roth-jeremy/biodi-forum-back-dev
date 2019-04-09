# BIODI-VERS-CITY.CH - API DOC
This is the documentation about the REST WP API allowing an external app to fetch data on the forum's SQL database for it to functionate properly.

The base URL to fetch the general JSON information is the following : https://forum.biodi-vers-city.ch/wp-json

It will be used as the base URL in the next steps of this document. Please append the given requests' PATH to this base to get expected results.

## USERS
#### Get all Users
````php
GET /wp/v2/users?context=edit
````
_Note: "?context=edit" allows us to retrieve all the informations about a user_

The following arguments can be passed in the QUERY parameters:
|name|type|description|
|--|--|--|
|context|String|Scope under which the request is made; determines fields present in response. <br>**Default :** `view` <br> **One of :** `view, embed, edit`|
|page|Number| Current page of the collection. <br>**Default :** 1
per_page| Number | 	Maximum number of items to be returned in result set. <br>**Default :** 10
search| String | Limit results to those matching a string.
exclude|Number|Ensure result set excludes specific IDs. 
include|Number| Limit result set to specific IDs.
offset|Number| Offset the result set by a specific number of items.
order| String| Order sort attribute ascending or descending. <br>**Default :** `asc`<br>**One of :** `asc, desc`
orderby| String| Sort collection by object attribute.<br>**Default :** `name`<br>**One of :** `id, include, name, registered_date, slug, email, url`
slug| String| Limit result set to users with one or more specific slugs. 
roles| String| Limit result set to users matching at least one specific role provided. Accepts csv list or single role. 

#### Get a User
 ````php
GET /wp/v2/users/<id>
````
The following arguments can be passed in the QUERY parameters:
|name|type|description|
|--|--|--|
|id|Number| Unique identifier for the user. 
|context|String|Scope under which the request is made; determines fields present in response. <br>**Default :** `view` <br> **One of :** `view, embed, edit`|
#### Create a User
````php
POST /wp/v2/users
````
