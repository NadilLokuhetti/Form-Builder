public function up()
{
    Schema::create('fields', function (Blueprint $table) {
        $table->id();
        $table->foreignId('form_id')->constrained()->onDelete('cascade');
        $table->string('label');
        $table->enum('type', ['text', 'textarea', 'checkbox', 'radio']);
        $table->boolean('required')->default(false);
        $table->integer('order')->default(0);
        $table->json('options')->nullable(); // for checkbox/radio
        $table->timestamps();
    });
}
