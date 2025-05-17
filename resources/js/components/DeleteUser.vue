<script setup lang="ts">
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { 
  Dialog,
  DialogTrigger,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter
} from '@/components/ui/dialog';
import { useForm } from '@inertiajs/vue3';
import { AlertTriangle } from 'lucide-vue-next';

const confirmingUserDeletion = ref(false);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  
  setTimeout(() => {
    const passwordInput = document.getElementById('password');
    if (passwordInput instanceof HTMLInputElement) {
      passwordInput.focus();
    }
  }, 250);
};

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
    },
    onError: () => {
      const passwordInput = document.getElementById('password');
      if (passwordInput instanceof HTMLInputElement) {
        passwordInput.focus();
      }
    },
    onFinish: () => {
      form.reset();
    },
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.reset();
};
</script>

<template>
  <section class="space-y-6 border-t border-border pt-6 mt-6">
    <HeadingSmall 
      title="Hapus Akun" 
      description="Setelah akun Anda dihapus, semua sumber dayanya dan datanya akan dihapus secara permanen."
    />

    <Button variant="destructive" @click="confirmUserDeletion">
      Hapus Akun
    </Button>

    <Dialog 
      :open="confirmingUserDeletion"
      @update:open="confirmingUserDeletion = $event"
    >
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>Anda yakin ingin menghapus akun Anda?</DialogTitle>
          <DialogDescription>
            Setelah akun Anda dihapus, semua sumber dayanya dan datanya akan dihapus secara permanen.
            Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
          </DialogDescription>
        </DialogHeader>

        <div class="mt-4">
          <div class="flex items-center gap-2 text-sm text-destructive mb-4">
            <AlertTriangle class="h-4 w-4" />
            <span>Tindakan ini tidak dapat dibatalkan!</span>
          </div>
          
          <form @submit.prevent="deleteUser">
            <div>
              <Label for="password" class="sr-only">Password</Label>
              <Input
                id="password"
                type="password"
                v-model="form.password"
                placeholder="Kata Sandi"
                class="w-full"
                autocomplete="current-password"
              />
              <InputError :message="form.errors.password" class="mt-2" />
            </div>
          </form>
        </div>

        <DialogFooter class="mt-4 flex space-x-2 sm:justify-end">
          <Button variant="outline" @click="closeModal">Batal</Button>
          <Button 
            variant="destructive" 
            @click="deleteUser" 
            :disabled="form.processing"
          >
            {{ form.processing ? 'Menghapus...' : 'Hapus Akun' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </section>
</template> 