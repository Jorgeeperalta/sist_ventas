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
                          v-model="editedItem.precio"
                          label="precio"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6" sm="6" md="6">
                        <v-text-field
                          v-model="editedItem.categoria"
                          label="Categoria"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="6" sm="6" md="6">
                        <v-text-field
                          v-model="editedItem.nombre"
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

        <template v-slot:item.precio="{ item }">
          <v-chip :color="getColor(item.precio)" dark>{{ item.precio }}</v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
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
    idd: "",
    categoriaa: "",
    nombree: "",
    precioo: "",
    headers: [
      {
        text: "Identificador",
        align: "start",
        sortable: false,
        value: "id",
      },

      { text: "categoria", value: "categoria" },
      { text: "Nombre", value: "nombre" },

      { text: "Percio", value: "precio" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    desserts: [],
    editedIndex: -1,
    editedItem: {
      id: "",
      precio: 0,
      categoria: 0,
      nombre: 0,
    },
    defaultItem: {
      id: "",
      precio: 0,
      categoria: 0,
      nombre: 0,
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
          "http://localhost:81/sist_ventas/src/backend/articulos.php"
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
      this.idd = item.id;
      this.categoriaa = item.categoria;
      this.nombree = item.nombre;
      this.precioo = item.precio;

      this.cargar_datos();
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      if (this.editedIndex > -1) {
        var formdata = new FormData();
        formdata.append("id", this.idd);
        formdata.append("categoria", this.categoriaa);
        formdata.append("nombre", this.nombree);
        formdata.append("precio", this.precioo);

        var requestOptions = {
          method: "PUT",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://localhost:81/sist_ventas/src/backend/articulos.php?id=" +
            this.editedItem.id +
            "&categoria=" +
            this.editedItem.categoria +
            "&nombre=" +
            this.editedItem.nombre +
            "&precio=" +
            this.editedItem.precio,
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result));

        this.cargar_datos();
      } else {
        var formdata = new FormData();

        formdata.append("id", this.editedItem.id);
        formdata.append("categoria", this.editedItem.categoria);
        formdata.append("nombre", this.editedItem.nombre);
        formdata.append("precio", this.editedItem.precio);

        var requestOptions = {
          method: "POST",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://localhost:81/sist_ventas/src/backend/articulos.php",
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result));

        this.cargar_datos();

        this.close();
      }
      this.close();

      alert(this.editedItem.precio);
    },
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
  },
};
</script>
