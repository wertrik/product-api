

Endpoints:<br />
/api/v1/product [GET]<br />
Vrátí všechny produkty.<br />

Product {<br />
"id": int,<br />
"name": string,<br />
"price": float,<br />
"created_at": 1985-04-12T23:20:50.52Z,<br />
"updated_at": 1985-04-12T23:20:50.52Z<br />
}[]<br />


/api/v1/product/{id} [GET]<br />
Vrátí produkt podle ID.<br />
Product {<br />
"id": int,<br />
"name": string,<br />
"price": float,<br />
"created_at": 1985-04-12T23:20:50.52Z,<br />
"updated_at": 1985-04-12T23:20:50.52Z<br />
}<br />


/api/v1/product [POST]<br />
Vloží nový produkt. Payload:<br />
{<br />
"name": string, <br />
"price": float<br />
}<br />

/api/v1/product/{id} [DELETE]<br />
Odstraní produkt<br />

/api/v1/product/{id} [PUT]<br />
Změna produktu. payload:<br />
{<br />
"name": string,<br />
"price": float<br />
}<br />

/api/v1/product      [PUT]<br />
Změna produktu. payload:<br />
{<br />
"id": int<br />
"name": string,<br />
"price": float<br />
}<br />
