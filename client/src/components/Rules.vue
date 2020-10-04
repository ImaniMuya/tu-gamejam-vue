<template>
  <div>
    <page-header>Rules</page-header>
    <pre class="md" ref="md">
Contact [Dr. Denning](mailto://jon.denning@taylor.edu) if you have any questions!

* The GameJam is for Taylor University students and alumni only or by invitation.
* All game content must be created from scratch, except for music, sounds, and photos.
All used content must have appropriate license and be properly cited.
* Each team can have up to two programmers and an unlimited number of non-programming members.
Non-programming members can help with audio, images, stories, levels, etc.,
but they may not write code or scripts or program the gaming engine.
* We will have a soft start on Thursday at 6PM, where teams are registered, the theme is chosen,
and game development may start except for coding (see clarification at end).
On Friday at 6PM, we will have a hard start. No code written _prior_ to the hard start can be used afterwards.
You are encouraged to practice before the hard start,
but each team must start their code base over from scratch at the hard start.
* You may use any gaming engine or write your own! Just be sure to provide instructions for running your game.
Engine code is the only exception to the no-code-before-hard-start rule above,
however you must clear it with Dr. Denning first!
* Participation is free, but you must pay a competition fee in order to compete for the prizes and to consume
the provided snacks and drinks.
* Each GameJam will have a particular theme. How you integrate the theme into your game is up to you.
* The designated participation areas are Euler 217/218.
    </pre>
    <div class="rules-layout" v-html="content">
    </div>
  </div>
</template>
<script>
import PageHeader from './sub-components/PageHeader.vue';
import marked from 'marked';
export default {
  name: "Rules",
  components: { PageHeader},
  data() {
    return {
      content: ''
    }
  },
  mounted() {
    let md = this.$refs.md.innerText;
    this.content = marked(md);
    this.$refs.md.innerText = "";
  }
};
</script>

<style>
.rules-layout {
  margin: 10px auto;
  display: flex;
  flex-flow: column;
  align-items: center;
  max-width: 50rem;
}

.rules-layout > p {
  text-align: center;
  font-size: 24px;
  font-weight: 400;
}

.rules-layout li {
  font-size: 18px;
  margin-bottom: .5em;
}

.md {
  margin: 0;
}

</style>