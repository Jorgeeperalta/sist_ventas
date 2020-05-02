<template>
  <v-layout align-center justify-center>
    <v-flex xs12 sm8 md6 lg5 xl4>
      <v-card>
        <v-toolbar dark color="purple darken-4">
          <v-toolbar-title>
            Login
          </v-toolbar-title>
        </v-toolbar>
        <v-card-text>
          <v-text-field
            v-model="email"
            autofocus
            color="purple lighten-2"
            label="Email"
            required
            background-color="black"
          >
          </v-text-field>
          <v-text-field
            v-model="password"
            type="password"
            color="purple lighten-2"
            label="Password"
            required
            background-color="black"
          >
          </v-text-field>
          <v-flex class="red--text" v-if="error">
            {{ error }}
          </v-flex>
        </v-card-text>
        <v-card-actions class="px-3 pb-3">
          <v-flex text-xs-right>
            <v-btn block @click="control()" color="purple darken-4"
              >Ingresar</v-btn
            >
            <!--  <v-btn @click="insertar()" color="primary">Insertar</v-btn> -->

            <v-text-field
              color="purple darken-4"
              loading
              disabled
            ></v-text-field>
          </v-flex>
        </v-card-actions>
      </v-card>
    </v-flex>
  </v-layout>
</template>
<script>
export default {
  data() {
    return {
      email: "",
      password: "",
      error: null,
      bandera: 0,
      datos: [],
    };
  },
  created() {
    this.ingresar();
  },
  methods: {
    ingresar() {
      var cont = 0;
      async function asyncData() {
        const response = await fetch(
          "http://jorgeperalta-001-site6.itempurl.com/login.php"
        );
        const data = await response.json();

        return data;
      }

      const result = asyncData();

      result.then((data) => {
        data.forEach((element) => {
          this.datos[cont] = element;
          cont++;
        });
      });
    },
    control() {
      this.datos.forEach((element) => {
        if (element.email == this.email && element.password == this.password) {
          if (element.estato == 0) {
            this.bandera = 0;
          } else if (element.estato == 1) {
            this.bandera = 1;
          } else {
            this.bandera = 2;
          }
        }
      });
    
      this.$emit("estado_login", this.bandera);
    },
    insertar() {
      var formdata = new FormData();
      formdata.append("email", this.email);
      formdata.append("password", this.password);

      var requestOptions = {
        method: "POST",
        body: formdata,
      };

      fetch(
        "http://localhost:81/sist_ventas/src/backend/post.php",
        requestOptions
      )
        .then((response) => response.text())
        .then((result) => console.log(result))
        .catch((error) => console.log("error", error));
    },
  },
};
</script>
