<template>
  <v-app>
    <template>
      <v-data-table
        :headers="headers"
        :items="desserts"
        sort-by="precio"
        class="elevation-1"
        :search="search"
        :custom-filter="filterOnlyCapsText"
      >
        <template v-slot:top>
          <v-toolbar flat color="black">
            <v-toolbar-title>Articulos</v-toolbar-title>

            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              label="Buscar (SOLO UN NUMERO)"
              class="mx-2"
            ></v-text-field>

            <v-spacer></v-spacer>

            <v-dialog v-model="dialog" max-width="500px">
              <template v-slot:activator="{ on }">
                <v-btn color="primary" dark class="mb-2" v-on="on"
                  >Nuevo Articulo</v-btn
                >
              </template>

              <v-card>
                <v-card-title>
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="6" sm="6" md="6">
                        <v-text-field
                          v-model="editedItem.id"
                          label="Id"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6" sm="6" md="6">
                        <v-text-field
                          v-model="editedItem.mane"
                          label="precio"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6" sm="6" md="6">
                        <v-text-field
                          v-model="editedItem.email"
                          label="Categoria"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6" sm="6" md="6">
                        <v-text-field
                          v-model="editedItem.created_at"
                          label="Nombre"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="close"
                    >Cancelar</v-btn
                  >
                  <v-btn color="blue darken-1" text @click="save"
                    >Guardar</v-btn
                  >
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>

        <template v-slot:[`item.precio`]="{ item }">
          <v-chip :color="getColor(item.id)" dark>{{ item.id }}</v-chip>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon small class="mr-2" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon small @click="deleteItem(item)">
            mdi-delete
          </v-icon>
        </template>
        <template v-slot:no-data>
          <v-btn color="primary" @click="initialize">Reset</v-btn>
        </template>
      </v-data-table>
    </template>
  </v-app>
</template>
<script>
export default {
  data: () => ({
    dialog: false,
    search: "",
    headers: [
      {
        text: "Identificador",
        align: "start",
        sortable: false,
        value: "id",
      },

      { text: "categoria", value: "name" },
      { text: "Nombre", value: "email" },

      { text: "Percio", value: "create_at" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    desserts: [],
    editedIndex: -1,
    editedItem: {
      id: "",
      name: 0,
      email: 0,
      created_at: 0,
    },
    defaultItem: {
      id: "",
      name: 0,
      email: 0,
      created_at: 0,
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Articulo" : "Edita Articulo";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
    },
  },

  created() {
    this.initialize();
  },

  methods: {
    cargar_datos() {
              

             var cont = 0;
      async function asyncData() {
        const response = await fetch(
          "http://localhost:81/rest-api-with-php-and-mysql/api.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.desserts = data;
        console.log(data);
      });


    },

    filterOnlyCapsText(value, search, item) {
      return (
        value != null &&
        search != null &&
        typeof value === "string" &&
        value
          .toString()
          .toLocaleUpperCase()
          .indexOf(search) !== -1
      );
    },
    getColor(precio) {
      if (precio > 400) return "purple darken-3";
      else if (precio > 200) return "teal darken-2";
      else return "blue darken-2";
    },
    initialize() {
      this.cargar_datos();
    },

    editItem(item) {
      this.editedIndex = this.desserts.indexOf(item);

      this.editedItem = Object.assign({}, item);
      this.dialog = true;

      // var formdata = new FormData();
      //  formdata.append("id", item.id);
      // formdata.append("categoria",item.categoria);
      //  formdata.append("nombre", item.nombre);
      // formdata.append("precio", item.precio);
      var formdata = new FormData();
      formdata.append("id", "9");
      formdata.append("categoria", "1");
      formdata.append("nombre", "pepsi 2.25l");
      formdata.append("precio", "810");

      var requestOptions = {
        method: "PUT",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/articulos.php?id=2&categoria=1&nombre=pepsi&precio=6000",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .catch((error) => console.log("error", error));

      this.cargar_datos();

      this.close();
    },

    /*  deleteItem(item) {
      const index = this.desserts.indexOf(item);
      alert(item.id);
      confirm("Are you sure you want to delete this item?") &&
        this.desserts.splice(index, 1);
    },*/

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    /* save() {
      if (this.editedIndex > -1) {
        Object.assign(this.desserts[this.editedIndex], this.editedItem);
      } else {
        this.desserts.push(this.editedItem);
      }
      this.close();

       alert(this.editedItem.precio);
    },*/
    deleteItem(item) {
      const index = this.desserts.indexOf(item);

      confirm("Are you sure you want to delete this item?") &&
        this.desserts.splice(index, 1);
      var formdata = new FormData();

      var requestOptions = {
        method: "DELETE",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://localhost:81/sist_ventas/src/backend/articulos.php?id=" +
          item.id,
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .catch((error) => console.log("error", error));

      this.cargar_datos();
    },
    save() {
     var myHeaders = new Headers();
myHeaders.append("name", "edu");
myHeaders.append("email", "edu@@@@");

var formdata = new FormData();
formdata.append("id", "");
formdata.append("name", "jorge");
formdata.append("email", "edu@");
formdata.append("created_at", "");

var requestOptions = {
  method: 'POST',
  headers: myHeaders,
  body: formdata,
  redirect: 'follow'
};

fetch("http://localhost:81/rest-api-with-php-and-mysql/api.php?id=&name=edu@&email=dxfsdfz&created_at", requestOptions)
  .then(response => response.text())
  .then(result => console.log(result))
  .catch(error => console.log('error', error));
    },
  },
};
</script>
