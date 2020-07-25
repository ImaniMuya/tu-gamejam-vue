<template>
  <div>
    <div id="imgModal" :class="{tucked: modalTucked}" @click="modalTucked = true">
      <img :src="modalImgSrc"/>
    </div>
    <div class="submission-container">
      <submission-display v-for="team in teams" :key="team.id"
        :pastEvent="pastEvent"
        :submission="submissions[team.id]"
        :team="team"
        @popup="popupImage"
      />
    </div>
  </div>
</template>

<script>
import SubmissionDisplay from '@/components/sub-components/SubmissionDisplay.vue';
export default {
  name: "Event",
  components: { SubmissionDisplay },
  props: [ "submissions", "teams", "pastEvent"],
  data() {
    return {
      modalTucked: true,
      modalImgSrc: "",
    }
  },
  methods: {
    popupImage($event) {
      this.modalImgSrc = $event;
      this.modalTucked = false;
    }
  }
}
</script>

<style scoped>

#imgModal {
  position: fixed;
  transition: transform .3s ease-in-out;
  transform: none;
  height: 100vh;
  width: 100vw;
  background-color: rgba(0,0,0,0.7);
  display: flex;
  flex-flow: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  top: 0;
  left: 0;
  z-index: 101;
  opacity: 1;
  cursor: pointer;

  transition: opacity .25s ease;
}

#imgModal.tucked {
  opacity: 0;
  visibility: hidden;
}

#imgModal > img {
  max-width: 90vw;
  max-height: 90vh;
}
</style>