<template>
  <v-app>
    <v-app-bar app color="purple darken-4" dark>
      <v-btn
        v-if="variable != 2"
        target="_blank"
        text
        @click.stop="drawer = !drawer"
      >
        <span class="mr-2">selector</span>
        <v-icon>mdi-open-in-new</v-icon>
      </v-btn>

      <v-spacer></v-spacer>
      <div class="d-flex align-center">
        <v-img
          alt="Vuetify Logo"
          class="shrink mr-2"
          contain
          src="https://cdn.vuetifyjs.com/images/logos/vuetify-logo-dark.png"
          transition="scale-transition"
          width="80"
        />
      </div>
    </v-app-bar>

    <v-sheet height="8000" class="overflow-hidden" style="position: relative">
      <v-content>
        <v-row class="text-center" v-if="variable == 2">
          <v-col cols="12">
            <v-img
              :src="require('./assets/logo.svg')"
              class="my-3"
              contain
              height="100"
            />
          </v-col>
        </v-row>

        <login @estado_login="variable = $event" v-if="variable == 2"></login>
        <di v-if="variable == 0 || 1">
          <ventas v-if="vent == true"></ventas>
          <articulos v-if="art == true && variable == 1"></articulos>
          <catalogo v-if="cate == true && variable == 1"></catalogo>
          <proveedores v-if="provee == true && variable == 1"></proveedores>
          <clientes v-if="clie == true && variable == 1"></clientes>
          <informe v-if="info == true && variable == 1"></informe>
           <deudores v-if="deu == true && variable == 1"></deudores>
        </di>
      </v-content>

      <v-navigation-drawer v-model="drawer" absolute temporary>
        <v-list-item>
          <v-list-item-avatar>
            <v-img :src="require('./assets/logo.svg')"></v-img>
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>Sis. Ventas</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-divider></v-divider>
        <v-btn block @click="elije_articulos()"
          >Articulos<v-icon></v-icon
        ></v-btn>
        <v-btn block @click="elije_categorias()"
          >Categorias<v-icon></v-icon
        ></v-btn>
        <v-btn block @click="elije_proveedores()"
          >Proveedores<v-icon></v-icon
        ></v-btn>
        <v-btn block @click="elije_informe()">Informe<v-icon></v-icon></v-btn>
        <v-btn block @click="elije_clientes()">Clientes<v-icon></v-icon></v-btn>
        <v-btn block @click="elije_ventas()">Ventas<v-icon></v-icon></v-btn>
          <v-btn block @click="elije_deudores()">Deudores<v-icon></v-icon></v-btn>
      </v-navigation-drawer>
    </v-sheet>
  </v-app>
</template>

<script>
import login from "@/components/Login.vue";
import articulos from "@/components/Articulo.vue";
import ventas from "@/components/Venta.vue";
import prueba from "@/components/prueba.vue";
import catalogo from "@/components/Catalogo.vue";
import proveedores from "@/components/proveedores.vue";
import clientes from "@/components/Clientes.vue";
import informe from "@/components/Informe.vue";
import deudores from "@/components/Deudores.vue";
export default {
  name: "App",

  components: {
    login,
    articulos,
    ventas,
    prueba,
    catalogo,
    proveedores,
    clientes,
    informe,
    deudores
  },

  data: () => ({
    info: false,
    clie: false,
    provee: false,
    art: false,
    vent: false,
    cate: false,
    variable: 2,
    estado: false,
    deu:false,

    drawer: null,
    items: [
      { title: "Home", icon: "dashboard" },
      { title: "About", icon: "question_answer" },
    ],
  }),
  methods: {
    elije_informe() {
      this.art = false;
      this.vent = false;
      this.cate = false;
      this.provee = false;
      this.info = true;
      this.clie = false;
      this.deu= false;
    },
    elije_proveedores() {
      this.art = false;
      this.vent = false;
      this.cate = false;
      this.provee = true;
      this.info = false;
      this.clie = false;
      this.deu= false;
    },
    elije_articulos() {
      this.art = true;
      this.vent = false;
      this.cate = false;
      this.provee = false;
      this.info = false;
      this.clie = false;
      this.deu= false;
    },
    elije_ventas() {
      this.vent = true;
      this.art = false;
      this.cate = false;
      this.provee = false;
      this.info = false;
      this.clie = false;
      this.deu= false;
    },
    elije_categorias() {
      this.vent = false;
      this.art = false;
      this.cate = true;
      this.provee = false;
      this.info = false;
      this.clie = false;
      this.deu= false;
    },
    elije_clientes() {
      this.vent = false;
      this.art = false;
      this.cate = false;
      this.provee = false;
      this.clie = true;
      this.info = false;
      this.deu= false;
    },
     elije_deudores() {
      this.vent = false;
      this.art = false;
      this.cate = false;
      this.provee = false;
      this.clie = false;
      this.info = false;
      this.deu= true;
    },
  },
};
</script>
<style scoped>
#v-app {
  background-color: black;
}
</style>
