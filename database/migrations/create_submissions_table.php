public function up()
{
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('form_id')->constrained()->onDelete('cascade');
        $table->string('ip_address')->nullable();
        $table->timestamps();
    });
}
