<template>
  <v-app>
    <v-card class="mx-auto" width="1200">
        <br />
      <v-label><h1 style="margin-left: 10px">Articulos</h1></v-label>
      <v-text-field color="purple darken-4" loading disabled></v-text-field>
      <template>
        <v-row>
          <v-col md="4" sm="4">
            <div style="margin-left: 30px">
              <label><b>Seleccione una Categoria Filtrar: </b></label>
              <br />
              <select
                @click="cargar_datos()"
                v-model="editedItem.categoria"
                class="select-css"
              >
                <option
                  v-for="(item, index) in items2"
                  :key="index"
                  v-bind:value="item.nombre"
                >
                  {{ item.nombre }}
                </option>
              </select>
            </div>
          </v-col>
          <v-col md="4" sm="4">
            <div>
              <label><b>Porcentaje a actualizar: </b></label>
            </div>

            <v-text-field
              @click="agrupaarticulos(editedItem.categoria)"
              v-model="porcentaje"
              label="Porcentaje"
            ></v-text-field>
          </v-col>
          <v-col md="4" sm="4">
            <v-btn
              outlined
              color="yellow"
              block
              @click="actualizaporcategoria(porcentaje, editedItem.categoria)"
              style="margin-top: 35px; margin-right: 10px"
              >Actualizar</v-btn
            >
          </v-col>
        </v-row>

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
                label="Buscar (SOLO UN PRODUCTO X IDENTIFICADOR)"
                class="mx-2"
              ></v-text-field>

              <v-spacer></v-spacer>
              <v-dialog max-width="500px" v-model="dialogstock">
                <v-card>
                  <v-card-title>
                    <label>Actualiza Stock de {{ unidadstock.nombre }}</label>
                  </v-card-title>
                  <v-card-text
                    ><v-text-field
                      v-model="stock"
                      label="Ingrese Cantidad"
                    ></v-text-field>
                    <br />
                    <v-btn
                      style="margin-left: 340px"
                      @click="unidadstockmetodo(stock)"
                      >Actualiza</v-btn
                    >
                  </v-card-text>
                </v-card>
              </v-dialog>

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
                          <select
                            v-model="editedItem.categoria"
                            class="select-css"
                          >
                            <option
                              v-for="(item, index) in items2"
                              :key="index"
                              v-bind:value="item.nombre"
                            >
                              {{ item.nombre }}
                            </option>
                          </select>
                        </v-col>
                        <v-col cols="6" sm="6" md="6">
                          <v-text-field
                            v-model="editedItem.nombre"
                            label="Nombre"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col sm="6" md="6">
                          <v-text-field
                            v-model="editedItem.cantidad"
                            label="Cantidad"
                          ></v-text-field>
                        </v-col>
                        <v-col sm="6" md="6">
                          <v-text-field
                            v-model="editedItem.stockminimo"
                            label="Stock Minimo"
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
            <v-chip :color="getColor(item.precio)" dark>{{
              item.precio
            }}</v-chip>
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-icon
              style="margin-right: 10px"
              small
              @click="actualizastock(item), (dialogstock = true)"
            >
              mdi-table-column-plus-before
            </v-icon>
            <v-icon small class="mr-2" @click="editItem(item)">
              mdi-pencil
            </v-icon>
            <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
          </template>
          <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Reset</v-btn>
          </template>
        </v-data-table>
      </template>
    </v-card>
  </v-app>
</template>
<script>
export default {
  data: () => ({
    stock: null,
    items2: [],
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
      { text: "Cantidad", value: "cantidad" },
       { text: "Stock Minimo", value: "stockminimo" },
      { text: "Fecha", value: "fecha"},
      { text: "Acciones", value: "actions", sortable: false },
    ],
    dialogstock: false,
    desserts: [],
    editedIndex: -1,
    unidadstock: [],
    editedItem: {
      id: "",
      precio: "",
      categoria: "",
      nombre: "",
      url: "",
      url2: "",
      url3: "",
      cantidad: "",
    },
    porcentaje: null,
    defaultItem: {
      id: "",
      precio: "",
      categoria: "",
      nombre: "",
      url: "",
      url2: "",
      url3: "",
      cantidad: "",
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
    this.autocompleta();
    this.initialize();
  },

  methods: {
    agrupaarticulos(categoria) {
      var art = new Array();
      this.desserts.forEach((element) => {
        if (element.categoria == categoria) art.push(element);
      });
      this.desserts = art;
    },

    actualizaporcategoria(porc, categoria) {
      porc = porc / 100 + 1;
      // alert(porc + categoria)

      var formdata = new FormData();

      var requestOptions = {
        method: "PUT",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/actualizaarticulos.php?categoria=" +
          categoria +
          "&valor=" +
          porc  +
          "&fecha=" +
          new Date().toISOString().substr(0, 10) +
          "",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .then((result) => console.log('Se actualizo con exito!!'))
        .catch((error) => console.log("error", error));

      this.cargar_datos();
      alert('Se actualizo con exito!!')
    },
    autocompleta() {
      var cont = 0;
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/catalogo.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        this.items2 = data;

        console.log(this.items2);
      });
    },
    cargar_datos() {
      var cont = 0;
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/articulos.php"
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
        value.toString().toLocaleUpperCase().indexOf(search) !== -1
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
    unidadstockmetodo(st) {
      var formdata = new FormData();

      var requestOptions = {
        method: "PUT",
        body: formdata,
        redirect: "follow",
      };

      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/actualizastock.php?id=" +
          this.unidadstock.id +
          "&stock=" +
           st +
          "&fecha=" +
           new Date().toISOString().substr(0, 10)+
          "",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .then((result) => alert('Se actulizo de exito!!'))
        .catch((error) => console.log("error", error));

      this.cargar_datos();
      this.dialogstock=false
    },
    actualizastock(item) {
      this.unidadstock = item;
      this.dialogstock = true;
    },
    save() {
      if (this.editedIndex > -1) {
        var requestOptions = {
          method: "PUT",
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/articulos.php?id=" +
            this.editedItem.id +
            "&categoria=" +
            this.editedItem.categoria +
            "&nombre=" +
            this.editedItem.nombre +
            "&precio=" +
            this.editedItem.precio +
            "&url=" +
            this.editedItem.url +
            "&url2=" +
            this.editedItem.url2 +
            "&url3=" +
            this.editedItem.url3 +
            "&cantidad=" +
            this.editedItem.cantidad+
            "&fecha=" +
             new Date().toISOString().substr(0, 10)+
            "&stockminimo=" +
             this.editedItem.stockminimo,
          requestOptions
        )
          .then((response) => response.text())
           .then((result) => alert('Se edito con exito!!'))

          .then((result) => console.log(result));

        this.cargar_datos();
      } else {
        var formdata = new FormData();

        formdata.append("id", this.editedItem.id);
        formdata.append("categoria", this.editedItem.categoria);
        formdata.append("nombre", this.editedItem.nombre);
        formdata.append("precio", this.editedItem.precio);
        formdata.append("url", this.editedItem.url);
        formdata.append("url2", this.editedItem.url2);
        formdata.append("url3", this.editedItem.url3);
        formdata.append("cantidad", this.editedItem.cantidad);
         formdata.append("fecha", new Date().toISOString().substr(0, 10));
         formdata.append("stockminimo", this.editedItem.stockminimo);
         
        var requestOptions = {
          method: "POST",
          body: formdata,
          redirect: "follow",
        };

        fetch(
          "http://jorgeperalta-001-site6.itempurl.com/articulos.php",
          requestOptions
        )
          .then((response) => response.text())
          .then((result) => console.log(result))
           .then((result) => alert('Se almaceno con exito!!'));

        this.cargar_datos();

        this.close();
      }
      this.close();
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
        "http://jorgeperalta-001-site6.itempurl.com/articulos.php?id=" +
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
<style scoped>
.select-css {
  display: block;
  font-size: 16px;
  font-family: "Verdana", sans-serif;
  font-weight: 400;
  color: rgb(234, 235, 225);
  line-height: 1.3;
  padding: 0.4em 1.4em 0.3em 0.8em;
  width: 400px;
  max-width: 100%;
  box-sizing: border-box;
  margin: 20px auto;
  border: 1px solid #aaa;
  box-shadow: 0 1px 0 1px rgba(0, 0, 0, 0.03);
  border-radius: 0.3em;
  -moz-appearance: none;
  -webkit-appearance: none;
  appearance: none;
  background-color: rgb(17, 17, 17);
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E"),
    linear-gradient(to bottom, #0f0f0f 0%, #0c0c0c 100%);
  background-repeat: no-repeat, repeat;
  background-position: right 0.7em top 50%, 0 0;
  background-size: 0.65em auto, 100%;
}
</style>
