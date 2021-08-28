<template>
   <v-card>
        <v-system-bar>
          <v-label>Filtro de busqueda </v-label>
        </v-system-bar>

        <div>
          <v-card class="d-flex flex-row mb-2" flat tile>
          

            <v-col  md="3" >
               <v-autocomplete
            style="margin-left: 10px; margin-top: 30px"
            v-model="value"
            :items="proveedores"
            item-text="nombre"
            item-value="id"
            dense
            filled
            label="Buscar por apellido"
          ></v-autocomplete>
            </v-col>

            <v-col
              md="3"
              
            >
              <v-select
                :items="tipoDeHecho"
                item-text="tipohecho"
                item-value="'pktipohecho'"
                label="Seleccione tipo de hecho"
                v-model="detalledelito"
                @click="asociarLugar(datos.localidad), focalizar()"
              ></v-select>
            </v-col>
            <v-col
              md="3"
              
            >
              <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="date"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on }">
                  <v-text-field
                    v-model="dateRangeText"
                    label="Rango de fechas"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker v-model="dates" range ref="picker">
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false"
                    >Cancel</v-btn
                  >
                  <v-btn text color="primary" @click="$refs.menu.save(date)"
                    >OK</v-btn
                  >
                </v-date-picker>
              </v-menu>
            </v-col>

            <v-col md="4" style="margin-top: 11px">
              <v-btn
                outlined
                @click="ponerdelitos(), focalizar(), (selectores = false)"
                >Buscar</v-btn
              >

              <v-btn
                v-if="vermapa == true"
                color="red"
                style="margin-left: 20px"
                outlined
                @click="restablecer(), (vermapa = false)"
                >Reset</v-btn
              >
            </v-col>

            <label
              v-if="selectores == false"
              style="margin-left: -200px; margin-top: 30px"
              >Fecha desde: {{ fdesde }} hasta: {{ fhasta }}</label
            >
          </v-card>
          <div style="margin-left: 950px"></div>
        </div>

        <template>
          <v-row>
            <v-col cols="12" sm="6">
              <v-dialog v-model="calendario" width="310px">
                <v-date-picker locale="es" v-model="dates" range>
                  <div style="margin-left: 210px">
                    <v-btn
                      color="primary"
                      @click="dar_formato(), (calendario = false)"
                      >OK</v-btn
                    >
                  </div>
                </v-date-picker>
              </v-dialog>
            </v-col>
          </v-row>
        </template>
      </v-card>
</template>

<script>
export default {
    data: () => ({
         fdesde: "",
         fhasta: "",
         calendario: null,
         datenow: new Date(),
         dates: ["2014-11-11", new Date().toISOString().substr(0, 10)],
         proveedores:[]
    }),
    mounted(){
      this.traerproveedores()
    },
    methods:{
        traerproveedores(){
              var requestOptions = {
        method: "GET",

        redirect: "follow",
      };
      //
      fetch(
        "http://jorgeperalta-001-site6.itempurl.com/proveedores.php",
        requestOptions
      )
        .then((response) => response.json())
        .then((result) => (this.proveedores = result))
        .catch((error) => console.log("error", error));
        }
    }

}
</script>

<style>

</style>