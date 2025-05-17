<template>
  <section class="bg-muted py-24 sm:py-32">
    <div class="container">
      <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
        <div>
          <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Contact Information</h2>
          <p class="mt-6 text-lg leading-8 text-muted-foreground">
            Get in touch with us through any of these channels.
          </p>
          <dl class="mt-10 space-y-4 text-base leading-7 text-muted-foreground">
            <div class="flex gap-x-4">
              <dt class="flex-none">
                <span class="sr-only">Address</span>
                <BuildingOffice2Icon class="h-7 w-6 text-primary" aria-hidden="true" />
              </dt>
              <dd>
                123 Business Street<br />
                Suite 100<br />
                City, State 12345
              </dd>
            </div>
            <div class="flex gap-x-4">
              <dt class="flex-none">
                <span class="sr-only">Telephone</span>
                <PhoneIcon class="h-7 w-6 text-primary" aria-hidden="true" />
              </dt>
              <dd>
                <a class="hover:text-foreground" href="tel:+1 (555) 234-5678">
                  +1 (555) 234-5678
                </a>
              </dd>
            </div>
            <div class="flex gap-x-4">
              <dt class="flex-none">
                <span class="sr-only">Email</span>
                <EnvelopeIcon class="h-7 w-6 text-primary" aria-hidden="true" />
              </dt>
              <dd>
                <a class="hover:text-foreground" href="mailto:hello@example.com">
                  hello@example.com
                </a>
              </dd>
            </div>
          </dl>
        </div>
        <div class="relative">
          <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-muted-foreground">
            <!-- Replace with your preferred map component or iframe -->
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.95373631531973!3d-37.817327679751734!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sBusiness%20Street%2C%20Melbourne%20VIC%203000%2C%20Australia!5e0!3m2!1sen!2sus!4v1635167261304!5m2!1sen!2sus"
              width="100%"
              height="100%"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
            ></iframe>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import {
  BuildingOffice2Icon,
  PhoneIcon,
  EnvelopeIcon
} from '@heroicons/vue/24/outline'
</script> 